import os
import base64
from io import BytesIO
from PIL import Image
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from transformers import pipeline
from ultralytics import YOLO
import uvicorn
import gradio as gr
import threading
import logging
import requests
import asyncio
import uuid

# ==============================
# Logging
# ==============================
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# ==============================
# Models
# ==============================
food_classifier = pipeline("image-classification", model="nateraw/food")
yolo_model = YOLO("yolov8n.pt")

# USDA API config
USDA_API_URL = "https://api.nal.usda.gov/fdc/v1/foods/search"
USDA_API_KEY = os.getenv("USDA_API_KEY", "qktfia6caeuBSww2A5SYns8NaLlE2OuozHaEASzw")

# FastAPI app
app = FastAPI()

# Request schema
class ImageRequest(BaseModel):
    image: str  # Base64 image
    portion_size: float = 100.0

# Decode base64 image
def decode_base64_image(base64_string):
    try:
        img_data = base64.b64decode(base64_string)
        img = Image.open(BytesIO(img_data)).convert("RGB")
        return img
    except Exception as e:
        logger.error(f"Image decoding failed: {str(e)}")
        raise HTTPException(status_code=400, detail="Invalid base64 image")

# Crop image to food or container
def crop_image_to_food(img, yolo_results, 
                       food_labels=[
                           # Existing labels
                           "chicken_curry", "pizza", "salad", "lasagna", "risotto",
                           # Korean foods
                           "kimchi", "bibimbap", "bulgogi", "japchae", "tteokbokki",
                           "samgyeopsal", "kimchi_jjigae", "doenjang_jjigae", "sundubu_jjigae",
                           "galbi", "kimbap", "jajangmyeon", "naengmyeon", "dakgalbi",
                           "haemul_pajeon", "gimbap", "samgyetang", "bossam", "seolleongtang",
                           "mandu", "yangnyeom_chicken", "gamjatang", "jokbal", "budae_jjigae",
                           "haemul_tang", "dongtae_jjigae", "kongguksu", "mul_naengmyeon",
                           "tteokguk", "miyeokguk",
                           # Japanese foods
                           "sushi", "ramen", "udon", "tempura", "sashimi", "onigiri",
                           "yakitori", "miso_soup", "okonomiyaki", "takoyaki",
                           "donburi", "gyudon", "katsu_curry", "soba", "tonkatsu",
                           "shabu_shabu", "natto", "unagi", "chawanmushi", "tamagoyaki",
                           "yakisoba", "omurice", "kare_raisu", "oyakodon", "gyoza",
                           # Chinese foods
                           "fried_rice", "dumplings", "mapo_tofu", "kung_pao_chicken",
                           "sweet_sour_pork", "chow_mein", "spring_rolls", "peking_duck",
                           "dim_sum", "hot_pot", "xiaolongbao", "char_siu", "wonton_soup",
                           "egg_foo_young", "beef_broccoli", "szechuan_chicken", "lo_mein",
                           "hunan_pork", "crispy_duck", "ma_la_tang", "dan_dan_noodles",
                           "zha_jiang_mian", "lion_head_meatballs",
                           # Thai foods
                           "pad_thai", "tom_yum", "green_curry", "red_curry", "som_tam",
                           "massaman_curry", "khao_soi", "pad_kra_pao", "tom_kha_gai",
                           "larb", "panang_curry", "pad_see_ew", "khao_man_gai",
                           "nam_tok", "gaeng_som", "khao_pad", "mango_sticky_rice",
                           "satay_chicken", "thai_fried_rice", "tod_man_pla", "kuay_teow",
                           # Indian foods
                           "butter_chicken", "biryani", "paneer_tikka", "dal", "naan",
                           "rogan_josh", "palak_paneer", "samosa", "chole", "tandoori_chicken",
                           "aloo_gobi", "vindaloo", "dosa", "idli", "vada",
                           "rajma", "pav_bhaji", "korma", "malai_kofta", "jalebi",
                           "paratha", "bhindi_masala", "chicken_tikka_masala",
                           # Other Asian foods
                           "pho", "banh_mi", "laksa", "nasi_goreng", "rendang", "satay",
                           "adobo", "sinigang", "hainan_chicken_rice", "char_kway_teow",
                           "lechon", "soto_ayam", "bubur_ayam", "nasi_lemak", "mee_goreng"
                       ], 
                       container_labels=["bowl", "plate", "dish"]):
    try:
        for result in yolo_results:
            for box, cls in zip(result.boxes.xyxy, result.boxes.cls):
                label = result.names[int(cls)]
                if label in food_labels:
                    x1, y1, x2, y2 = map(int, box)
                    return img.crop((x1, y1, x2, y2)), True
        for result in yolo_results:
            for box, cls in zip(result.boxes.xyxy, result.boxes.cls):
                label = result.names[int(cls)]
                if label in container_labels:
                    x1, y1, x2, y2 = map(int, box)
                    return img.crop((x1, y1, x2, y2)), True
        return img, False
    except Exception as e:
        logger.error(f"Cropping failed: {str(e)}")
        return img, False

# Calculate nutrients
def calculate_nutrients(food_items, portion_size):
    nutrients = {"protein": 0, "carbs": 0, "fat": 0, "fiber": 0, "sodium": 0}
    micronutrients = {"vitamin_c": 0, "calcium": 0, "iron": 0}
    top_food = max(food_items, key=food_items.get, default=None)
    if not top_food:
        return nutrients, micronutrients, 0

    query_food = top_food.replace("_", " ")
    try:
        response = requests.get(USDA_API_URL, params={
            "api_key": USDA_API_KEY,
            "query": query_food,
            "pageSize": 1
        })
        response.raise_for_status()
        data = response.json()
        if not data.get("foods"):
            return nutrients, micronutrients, 0

        food_data = data["foods"][0]
        food_nutrients = {n["nutrientName"]: n["value"] for n in food_data["foodNutrients"]}

        nutrients = {
            "protein": food_nutrients.get("Protein", 0) * (portion_size / 100),
            "carbs": food_nutrients.get("Carbohydrate, by difference", 0) * (portion_size / 100),
            "fat": food_nutrients.get("Total lipid (fat)", 0) * (portion_size / 100),
            "fiber": food_nutrients.get("Fiber, total dietary", 0) * (portion_size / 100),
            "sodium": food_nutrients.get("Sodium, Na", 0) * (portion_size / 100),
        }
        micronutrients = {
            "vitamin_c": food_nutrients.get("Vitamin C, total ascorbic acid", 0) * (portion_size / 100),
            "calcium": food_nutrients.get("Calcium, Ca", 0) * (portion_size / 100),
            "iron": food_nutrients.get("Iron, Fe", 0) * (portion_size / 100),
        }
        calories = (nutrients["protein"] * 4) + (nutrients["carbs"] * 4) + (nutrients["fat"] * 9)
        return nutrients, micronutrients, calories
    except Exception as e:
        logger.error(f"USDA API request failed: {str(e)}")
        return nutrients, micronutrients, 0

# ==============================
# FastAPI endpoint
# ==============================
@app.post("/analyze_food")
async def analyze_food(request: ImageRequest):
    try:
        img = decode_base64_image(request.image)
        yolo_results = yolo_model(img)
        cropped_img, was_cropped = crop_image_to_food(img, yolo_results)

        # Food classification
        food_results = food_classifier(cropped_img)
        food_items = {r["label"]: r["score"] for r in food_results if r["score"] >= 0.3}

        # Whitelist food labels
        food_label_whitelist = [
            # Existing labels
            "pizza", "salad", "chicken", "chicken_wings", "shrimp_and_grits",
            "lasagna", "risotto", "burger", "sandwich", "pasta",
            # Korean foods
            "kimchi", "bibimbap", "bulgogi", "japchae", "tteokbokki",
            "samgyeopsal", "kimchi_jjigae", "doenjang_jjigae", "sundubu_jjigae",
            "galbi", "kimbap", "jajangmyeon", "naengmyeon", "dakgalbi",
            "haemul_pajeon", "gimbap", "samgyetang", "bossam", "seolleongtang",
            "mandu", "yangnyeom_chicken", "gamjatang", "jokbal", "budae_jjigae",
            "haemul_tang", "dongtae_jjigae", "kongguksu", "mul_naengmyeon",
            "tteokguk", "miyeokguk",
            # Japanese foods
            "sushi", "ramen", "udon", "tempura", "sashimi", "onigiri",
            "yakitori", "miso_soup", "okonomiyaki", "takoyaki",
            "donburi", "gyudon", "katsu_curry", "soba", "tonkatsu",
            "shabu_shabu", "natto", "unagi", "chawanmushi", "tamagoyaki",
            "yakisoba", "omurice", "kare_raisu", "oyakodon", "gyoza",
            # Chinese foods
            "fried_rice", "dumplings", "mapo_tofu", "kung_pao_chicken",
            "sweet_sour_pork", "chow_mein", "spring_rolls", "peking_duck",
            "dim_sum", "hot_pot", "xiaolongbao", "char_siu", "wonton_soup",
            "egg_foo_young", "beef_broccoli", "szechuan_chicken", "lo_mein",
            "hunan_pork", "crispy_duck", "ma_la_tang", "dan_dan_noodles",
            "zha_jiang_mian", "lion_head_meatballs",
            # Thai foods
            "pad_thai", "tom_yum", "green_curry", "red_curry", "som_tam",
            "massaman_curry", "khao_soi", "pad_kra_pao", "tom_kha_gai",
            "larb", "panang_curry", "pad_see_ew", "khao_man_gai",
            "nam_tok", "gaeng_som", "khao_pad", "mango_sticky_rice",
            "satay_chicken", "thai_fried_rice", "tod_man_pla", "kuay_teow",
            # Indian foods
            "butter_chicken", "biryani", "paneer_tikka", "dal", "naan",
            "rogan_josh", "palak_paneer", "samosa", "chole", "tandoori_chicken",
            "aloo_gobi", "vindaloo", "dosa", "idli", "vada",
            "rajma", "pav_bhaji", "korma", "malai_kofta", "jalebi",
            "paratha", "bhindi_masala", "chicken_tikka_masala",
            # Other Asian foods
            "pho", "banh_mi", "laksa", "nasi_goreng", "rendang", "satay",
            "adobo", "sinigang", "hainan_chicken_rice", "char_kway_teow",
            "lechon", "soto_ayam", "bubur_ayam", "nasi_lemak", "mee_goreng"
        ]

        non_food_items = [
            r.names[int(cls)]
            for r in yolo_results
            for cls in r.boxes.cls
            if r.names[int(cls)] not in food_items and r.names[int(cls)] not in food_label_whitelist
        ]

        is_non_food = len(non_food_items) > len(food_items) and max(food_items.values(), default=0) < 0.5

        nutrients, micronutrients, calories = calculate_nutrients(food_items, request.portion_size)

        ingredient_map = {
            # Existing mappings
            "pizza": ["dough", "tomato sauce", "cheese"],
            "salad": ["lettuce", "tomato", "cucumber"],
            "chicken_curry": ["chicken", "curry sauce", "spices"],
            "lasagna": ["pasta", "tomato sauce", "cheese", "meat"],
            "risotto": ["rice", "broth", "parmesan"],
            # Korean foods
            "kimchi": ["napa cabbage", "chili powder", "garlic", "ginger"],
            "bibimbap": ["rice", "mixed vegetables", "gochujang", "egg"],
            "bulgogi": ["beef", "soy sauce", "garlic", "sesame oil"],
            "japchae": ["sweet potato noodles", "vegetables", "soy sauce", "beef"],
            "tteokbokki": ["rice cakes", "red chili paste", "fish cakes"],
            "samgyeopsal": ["pork belly", "garlic", "sesame oil"],
            "kimchi_jjigae": ["kimchi", "pork", "tofu", "green onions"],
            "doenjang_jjigae": ["soybean paste", "tofu", "vegetables", "mushrooms"],
            "sundubu_jjigae": ["soft tofu", "seafood", "chili paste", "egg"],
            "galbi": ["short ribs", "soy sauce", "garlic", "sugar"],
            "kimbap": ["rice", "seaweed", "vegetables", "meat"],
            "jajangmyeon": ["noodles", "black bean sauce", "pork", "vegetables"],
            "naengmyeon": ["buckwheat noodles", "beef broth", "cucumber", "egg"],
            "dakgalbi": ["chicken", "gochujang", "cabbage", "sweet potato"],
            "haemul_pajeon": ["seafood", "green onions", "flour", "egg"],
            "gimbap": ["rice", "seaweed", "carrot", "spinach"],
            "samgyetang": ["chicken", "ginseng", "jujube", "rice"],
            "bossam": ["pork belly", "cabbage", "garlic", "ssamjang"],
            "seolleongtang": ["beef", "bone broth", "noodles", "green onions"],
            "mandu": ["dumpling wrapper", "pork", "cabbage", "garlic"],
            "yangnyeom_chicken": ["chicken", "gochujang", "soy sauce", "honey"],
            "gamjatang": ["pork spine", "potato", "perilla leaves", "chili"],
            "jokbal": ["pig's feet", "soy sauce", "ginger", "garlic"],
            "budae_jjigae": ["sausage", "spam", "kimchi", "noodles"],
            "haemul_tang": ["seafood", "radish", "chili", "broth"],
            "dongtae_jjigae": ["pollack", "tofu", "radish", "chili"],
            "kongguksu": ["soybean noodles", "soy milk", "cucumber", "sesame"],
            "mul_naengmyeon": ["buckwheat noodles", "cold broth", "beef", "egg"],
            "tteokguk": ["rice cake", "beef broth", "egg", "seaweed"],
            "miyeokguk": ["seaweed", "beef", "soy sauce", "garlic"],
            # Japanese foods
            "sushi": ["rice", "raw fish", "seaweed", "vinegar"],
            "ramen": ["noodles", "broth", "pork", "seaweed"],
            "udon": ["thick noodles", "broth", "green onions", "fish cake"],
            "tempura": ["shrimp", "vegetables", "batter", "soy dipping sauce"],
            "sashimi": ["raw fish", "soy sauce", "wasabi"],
            "onigiri": ["rice", "seaweed", "fish", "pickled plum"],
            "yakitori": ["chicken", "skewers", "soy sauce", "mirin"],
            "miso_soup": ["miso paste", "tofu", "seaweed", "green onions"],
            "okonomiyaki": ["cabbage", "batter", "sauce", "bonito flakes"],
            "takoyaki": ["octopus", "batter", "sauce", "bonito flakes"],
            "donburi": ["rice", "meat", "egg", "onion"],
            "gyudon": ["beef", "rice", "onion", "soy sauce"],
            "katsu_curry": ["breaded cutlet", "curry sauce", "rice"],
            "soba": ["buckwheat noodles", "soy dipping sauce", "green onions"],
            "tonkatsu": ["pork cutlet", "bread crumbs", "cabbage", "sauce"],
            "shabu_shabu": ["beef", "vegetables", "broth", "dipping sauce"],
            "natto": ["fermented soybeans", "soy sauce", "mustard"],
            "unagi": ["eel", "soy sauce", "mirin", "rice"],
            "chawanmushi": ["egg custard", "shrimp", "mushrooms", "gingko"],
            "tamagoyaki": ["egg", "soy sauce", "mirin", "sugar"],
            "yakisoba": ["noodles", "pork", "cabbage", "sauce"],
            "omurice": ["rice", "egg", "ketchup", "chicken"],
            "kare_raisu": ["curry", "rice", "carrot", "potato"],
            "oyakodon": ["chicken", "egg", "onion", "rice"],
            "gyoza": ["dumpling wrapper", "pork", "cabbage", "garlic"],
            # Chinese foods
            "fried_rice": ["rice", "egg", "vegetables", "soy sauce"],
            "dumplings": ["pork", "cabbage", "wrapper", "ginger"],
            "mapo_tofu": ["tofu", "ground pork", "sichuan pepper", "chili oil"],
            "kung_pao_chicken": ["chicken", "peanuts", "chili peppers", "soy sauce"],
            "sweet_sour_pork": ["pork", "pineapple", "bell peppers", "sweet sour sauce"],
            "chow_mein": ["noodles", "vegetables", "meat", "soy sauce"],
            "spring_rolls": ["wrapper", "cabbage", "carrot", "pork"],
            "peking_duck": ["duck", "pancakes", "hoisin sauce", "cucumber"],
            "dim_sum": ["various fillings", "wrapper", "bamboo steamer"],
            "hot_pot": ["broth", "beef", "vegetables", "tofu"],
            "xiaolongbao": ["pork", "soup", "wrapper", "ginger"],
            "char_siu": ["pork", "honey", "soy sauce", "hoisin"],
            "wonton_soup": ["wontons", "broth", "shrimp", "pork"],
            "egg_foo_young": ["egg", "vegetables", "meat", "gravy"],
            "beef_broccoli": ["beef", "broccoli", "soy sauce", "garlic"],
            "szechuan_chicken": ["chicken", "sichuan pepper", "chili", "peanuts"],
            "lo_mein": ["noodles", "vegetables", "meat", "soy sauce"],
            "hunan_pork": ["pork", "chili", "garlic", "soy sauce"],
            "crispy_duck": ["duck", "soy sauce", "spices", "hoisin"],
            "ma_la_tang": ["broth", "noodles", "vegetables", "spices"],
            "dan_dan_noodles": ["noodles", "pork", "sichuan pepper", "peanut sauce"],
            "zha_jiang_mian": ["noodles", "pork", "bean sauce", "cucumber"],
            "lion_head_meatballs": ["pork", "water chestnuts", "egg", "broth"],
            # Thai foods
            "pad_thai": ["rice noodles", "shrimp", "tamarind paste", "peanuts"],
            "tom_yum": ["shrimp", "lemongrass", "chili", "galangal"],
            "green_curry": ["coconut milk", "green chili", "chicken", "bamboo shoots"],
            "red_curry": ["coconut milk", "red chili", "chicken", "basil"],
            "som_tam": ["green papaya", "chili", "lime", "fish sauce"],
            "massaman_curry": ["coconut milk", "beef", "potatoes", "peanuts"],
            "khao_soi": ["egg noodles", "coconut curry", "chicken", "chili"],
            "pad_kra_pao": ["basil", "chicken", "chili", "fish sauce"],
            "tom_kha_gai": ["coconut milk", "chicken", "galangal", "lemongrass"],
            "larb": ["minced pork", "lime", "fish sauce", "chili"],
            "panang_curry": ["coconut milk", "peanut", "chicken", "chili"],
            "pad_see_ew": ["wide noodles", "soy sauce", "chicken", "broccoli"],
            "khao_man_gai": ["chicken", "rice", "cucumber", "chili sauce"],
            "nam_tok": ["beef", "lime", "fish sauce", "chili"],
            "gaeng_som": ["fish", "tamarind", "chili", "vegetables"],
            "khao_pad": ["rice", "shrimp", "egg", "soy sauce"],
            "mango_sticky_rice": ["sticky rice", "mango", "coconut milk"],
            "satay_chicken": ["chicken", "peanut sauce", "skewers"],
            "thai_fried_rice": ["rice", "shrimp", "egg", "fish sauce"],
            "tod_man_pla": ["fish cakes", "chili paste", "lime leaves"],
            "kuay_teow": ["rice noodles", "broth", "beef", "herbs"],
            # Indian foods
            "butter_chicken": ["chicken", "tomato", "butter", "cream"],
            "biryani": ["rice", "chicken", "spices", "yogurt"],
            "paneer_tikka": ["paneer", "spices", "yogurt", "bell peppers"],
            "dal": ["lentils", "spices", "tomato", "ghee"],
            "naan": ["flour", "yeast", "butter", "yogurt"],
            "rogan_josh": ["lamb", "yogurt", "spices", "tomato"],
            "palak_paneer": ["spinach", "paneer", "spices", "cream"],
            "samosa": ["pastry", "potato", "peas", "spices"],
            "chole": ["chickpeas", "tomato", "spices", "onion"],
            "tandoori_chicken": ["chicken", "yogurt", "spices", "lemon"],
            "aloo_gobi": ["potato", "cauliflower", "spices", "tomato"],
            "vindaloo": ["pork", "vinegar", "chili", "spices"],
            "dosa": ["rice batter", "lentils", "potato filling"],
            "idli": ["rice", "lentils", "steamed"],
            "vada": ["lentils", "spices", "fried"],
            "rajma": ["kidney beans", "tomato", "spices", "onion"],
            "pav_bhaji": ["mixed vegetables", "spices", "butter", "bun"],
            "korma": ["chicken", "yogurt", "cream", "spices"],
            "malai_kofta": ["paneer balls", "cream", "tomato", "spices"],
            "jalebi": ["flour", "sugar syrup", "saffron"],
            "paratha": ["flour", "ghee", "stuffed vegetables"],
            "bhindi_masala": ["okra", "spices", "tomato", "onion"],
            "chicken_tikka_masala": ["chicken", "tomato", "cream", "spices"],
            # Other Asian foods
            "pho": ["rice noodles", "beef", "broth", "herbs"],
            "banh_mi": ["baguette", "pork", "pickled vegetables", "cilantro"],
            "laksa": ["coconut milk", "noodles", "chicken", "chili"],
            "nasi_goreng": ["rice", "chicken", "shrimp paste", "egg"],
            "rendang": ["beef", "coconut milk", "lemongrass", "spices"],
            "satay": ["chicken", "peanut sauce", "skewers", "soy sauce"],
            "adobo": ["chicken", "soy sauce", "vinegar", "garlic"],
            "sinigang": ["pork", "tamarind", "vegetables", "broth"],
            "hainan_chicken_rice": ["chicken", "rice", "cucumber", "chili sauce"],
            "char_kway_teow": ["flat noodles", "shrimp", "soy sauce", "egg"],
            "lechon": ["roast pig", "garlic", "lemongrass"],
            "soto_ayam": ["chicken", "noodles", "turmeric", "broth"],
            "bubur_ayam": ["rice porridge", "chicken", "ginger", "green onions"],
            "nasi_lemak": ["coconut rice", "sambal", "egg", "anchovies"],
            "mee_goreng": ["noodles", "chicken", "soy sauce", "chili"]
        }

        ingredients = [
            {"name": food, "probability": prob, "subclasses": ingredient_map.get(food.lower(), [])}
            for food, prob in food_items.items()
        ]

        return {
            "is_non_food": is_non_food,
            "non_food_items": non_food_items,
            "food_items": food_items,
            "ingredients": ingredients,
            "nutrients": nutrients,
            "macronutrients": {
                "protein": {"value": nutrients["protein"], "unit": "g"},
                "carbs": {"value": nutrients["carbs"], "unit": "g"},
                "fat": {"value": nutrients["fat"], "unit": "g"},
            },
            "micronutrients": micronutrients,
            "calories": calories,
            "source": "huggingface",
            "was_cropped": was_cropped
        }
    except Exception as e:
        logger.error(f"Analysis failed: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

# ==============================
# Gradio interface
# ==============================
def gradio_analyze(image):
    try:
        buffered = BytesIO()
        image.save(buffered, format="JPEG")
        base64_image = base64.b64encode(buffered.getvalue()).decode()
        request = ImageRequest(image=base64_image, portion_size=100.0)

        # Always create a new event loop
        loop = asyncio.new_event_loop()
        asyncio.set_event_loop(loop)
        result = loop.run_until_complete(analyze_food(request))
        loop.close()
        return result

    except Exception as e:
        return {"error": str(e)}

iface = gr.Interface(
    fn=gradio_analyze,
    inputs=gr.Image(type="pil"),
    outputs="json",
    title="Food Analysis API",
    description="Upload an image to analyze food items, non-food items, and nutritional content."
)

if __name__ == "__main__":
    threading.Thread(target=lambda: uvicorn.run(app, host="0.0.0.0", port=8000)).start()
    iface.launch(server_name="0.0.0.0", server_port=7860, share=True)
