import sys
import json
import tensorflow as tf
from tensorflow.keras.applications.efficientnet import EfficientNetB0, preprocess_input, decode_predictions
from tensorflow.keras.preprocessing.image import load_img, img_to_array
import numpy as np

def load_model():
    model = EfficientNetB0(weights='imagenet')
    return model

def detect_foods(image_path):
    model = load_model()
    img = load_img(image_path, target_size=(224, 224))
    img_array = img_to_array(img)
    img_array = np.expand_dims(img_array, axis=0)
    img_array = preprocess_input(img_array)
    
    predictions = model.predict(img_array)
    decoded = decode_predictions(predictions, top=3)[0]
    
    # Map ImageNet labels to food items (simplified)
    foods = [label for _, label, _ in decoded if 'food' in label.lower() or label in ['pizza', 'burger', 'salad']]  # Customize as needed
    return foods if foods else ['unknown']

if __name__ == "__main__":
    image_path = sys.argv[1]
    try:
        foods = detect_foods(image_path)
        print(json.dumps({'foods': foods}))
    except Exception as e:
        print(json.dumps({'error': str(e)}), file=sys.stderr)
        sys.exit(1)