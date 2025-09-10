import sys
import json
import base64
import os
import re
from gradio_client import Client, handle_file

def is_valid_base64(s):
    """Validate if the input string is a valid base64 string."""
    try:
        s = s.strip()
        if len(s) % 4 != 0:
            return False, f"Base64 string length ({len(s)}) is not a multiple of 4"
        if not re.match(r'^[A-Za-z0-9+/=]+$', s):
            return False, "Base64 string contains invalid characters"
        base64.b64decode(s, validate=True)
        return True, ""
    except Exception as e:
        return False, f"Invalid base64-encoded string: {str(e)}"

def predict(image_base64_path, hf_token=None, portion_size=100.0):
    try:
        with open(image_base64_path, "r") as f:
            image_base64 = f.read().strip()

        is_valid, error_msg = is_valid_base64(image_base64)
        if not is_valid:
            raise ValueError(error_msg)

        client = Client("versatile153/nutri", hf_token=hf_token)
        print(f"Loaded as API: https://versatile153-nutri.hf.space/ ✔", flush=True)

        tmp_path = "temp_image.jpg"
        with open(tmp_path, "wb") as f:
            f.write(base64.b64decode(image_base64))

        if not os.path.exists(tmp_path) or os.path.getsize(tmp_path) == 0:
            raise ValueError("Failed to create valid image file from base64 data")

        result = client.predict(handle_file(tmp_path))

        if os.path.exists(tmp_path):
            os.remove(tmp_path)

        if not isinstance(result, dict):
            raise ValueError("API returned invalid result format")

        food_items = result.get("food_items", {})
        top_food = max(food_items, key=food_items.get, default="unknown") if food_items else "unknown"
        is_non_food = result.get("is_non_food", False)
        non_food_items = result.get("non_food_items", [])
        ingredients = result.get("ingredients", [])
        nutrients = result.get("nutrients", {})
        micronutrients = result.get("micronutrients", {})
        calories = result.get("calories", 0)
        was_cropped = result.get("was_cropped", False)

        # Ensure all nutrient fields are present, using fallback if missing
        required_nutrients = ["protein", "carbs", "fat", "fiber", "sodium"]
        if not all(key in nutrients for key in required_nutrients) and top_food != "unknown":
            default_nutrients = {
                "pizza": {"protein": 12, "carbs": 35, "fat": 10, "fiber": 2, "sodium": 600},
                "salad": {"protein": 2, "carbs": 5, "fat": 1, "fiber": 3, "sodium": 100},
                "chicken_curry": {"protein": 20, "carbs": 15, "fat": 8, "fiber": 2, "sodium": 500},
                "lasagna": {"protein": 15, "carbs": 30, "fat": 12, "fiber": 3, "sodium": 700},
                "risotto": {"protein": 8, "carbs": 40, "fat": 5, "fiber": 1, "sodium": 400}
            }
            base_nutrients = default_nutrients.get(top_food.lower(), {"protein": 2, "carbs": 5, "fat": 1, "fiber": 2, "sodium": 100})
            nutrients = {k: base_nutrients.get(k, 0) for k in required_nutrients}

        # Scale nutrients based on portion_size
        if portion_size != 100.0:
            nutrients = {k: v * (portion_size / 100.0) for k, v in nutrients.items()}
            calories = (nutrients.get("protein", 0) * 4) + (nutrients.get("carbs", 0) * 4) + (nutrients.get("fat", 0) * 9)

        # Ensure ingredients if nutrients are from fallback
        if not ingredients and top_food != "unknown":
            ingredients = [{"name": top_food, "probability": food_items.get(top_food, 0), "subclasses": ["dough", "tomato sauce", "cheese"] if top_food == "pizza" else []}]

        # Fallback micronutrients
        if not micronutrients:
            micronutrients = {"vitamin_c": 19.38, "calcium": 324.04, "iron": 5.85} if top_food == "pizza" else {"vitamin_c": 0, "calcium": 0, "iron": 0}

        return json.dumps({
            "success": True,
            "result": {
                "is_non_food": is_non_food,
                "food_items": food_items,
                "non_food_items": non_food_items,
                "ingredients": ingredients,
                "nutrients": nutrients,
                "micronutrients": micronutrients,
                "calories": round(calories, 2),
                "portion_size": portion_size,
                "was_cropped": was_cropped
            }
        })
    except Exception as e:
        tmp_path = "temp_image.jpg"
        if 'tmp_path' in locals() and os.path.exists(tmp_path):
            os.remove(tmp_path)
        return json.dumps({"success": False, "error": str(e)})

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"success": False, "error": "No image file path provided"}))
        sys.exit(1)

    image_base64_path = sys.argv[1]
    hf_token = sys.argv[2] if len(sys.argv) > 2 else None
    portion_size = float(sys.argv[3]) if len(sys.argv) > 3 else 100.0

    if not os.path.exists(image_base64_path):
        print(json.dumps({"success": False, "error": f"Base64 file not found: {image_base64_path}"}))
        sys.exit(1)

    print(predict(image_base64_path, hf_token, portion_size))