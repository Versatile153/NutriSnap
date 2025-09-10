import cv2
import torch
import sys
import json
import os
import logging

# Configure logging
logging.basicConfig(
    filename='/home/apexgnoa/bincone.apexjets.org/storage/logs/opencv.log',
    level=logging.DEBUG,
    format='%(asctime)s %(levelname)s: %(message)s'
)

def detect_food(image_path):
    try:
        # Validate image path
        if not os.path.exists(image_path):
            logging.error(f"Image path does not exist: {image_path}")
            return {'is_food': False, 'foods': {}, 'error': f'Image file not found: {image_path}'}

        # Read and validate image
        img = cv2.imread(image_path)
        if img is None:
            logging.error(f"Failed to read image: {image_path}")
            return {'is_food': False, 'foods': {}, 'error': f'Failed to read image: {image_path}'}

        # Verify image format
        _, ext = os.path.splitext(image_path)
        if ext.lower() not in ['.jpg', '.jpeg', '.png']:
            logging.error(f"Unsupported image format: {ext}")
            return {'is_food': False, 'foods': {}, 'error': f'Unsupported image format: {ext}'}

        logging.debug(f"Processing image: {image_path}, size: {os.path.getsize(image_path)} bytes")

        # Load YOLOv5 model
        try:
            model = torch.hub.load('ultralytics/yolov5', 'yolov5s', pretrained=True)
        except Exception as e:
            logging.error(f"Failed to load YOLOv5 model: {str(e)}")
            return {'is_food': False, 'foods': {}, 'error': f'Failed to load YOLOv5 model: {str(e)}'}

        # Perform detection
        results = model(img)
        labels = results.xyxy[0][:, -1].cpu().numpy()
        confidences = results.xyxy[0][:, 4].cpu().numpy()
        food_items = {}
        food_keywords = [
            'pizza', 'salad', 'sandwich', 'burger', 'pasta', 'sushi', 'cake', 'bread',
            'chicken', 'beef', 'fish', 'rice', 'fruit', 'vegetable', 'soup', 'dessert'
        ]

        for label, confidence in zip(labels, confidences):
            label_name = model.names[int(label)]
            if label_name.lower() in food_keywords and confidence > 0.5:
                food_items[label_name] = float(confidence)

        logging.info(f"Detected food items: {food_items}")
        return {'is_food': bool(food_items), 'foods': food_items, 'error': None}

    except Exception as e:
        logging.error(f"Unexpected error in detect_food: {str(e)}")
        return {'is_food': False, 'foods': {}, 'error': f'Unexpected error: {str(e)}'}

if __name__ == '__main__':
    try:
        if len(sys.argv) != 2:
            logging.error("No image path provided")
            print(json.dumps({'is_food': False, 'foods': {}, 'error': 'No image path provided'}))
            sys.exit(1)

        image_path = sys.argv[1]
        result = detect_food(image_path)
        print(json.dumps(result))
    except Exception as e:
        logging.error(f"Script execution failed: {str(e)}")
        print(json.dumps({'is_food': False, 'foods': {}, 'error': f'Script execution failed: {str(e)}'}))
        sys.exit(1)
