from flask import Flask, request, jsonify
from flask_cors import CORS
import torch
from PIL import Image
import io
import base64

app = Flask(__name__)
CORS(app)

# Cargar el modelo YOLO
model = torch.hub.load('ultralytics/yolov5', 'custom', path='my_model.pt')

@app.route('/detect', methods=['POST'])
def detect():
    if 'image' not in request.files:
        return jsonify({'error': 'No se envió una imagen'}), 400

    # Leer imagen
    image_file = request.files['image']
    img = Image.open(image_file.stream)

    # Procesar con YOLO
    results = model(img)

    # Extraer resultados
    detections = []
    for *box, conf, cls in results.xyxy[0]:
        detections.append({
            "name": model.names[int(cls)],
            "confidence": float(conf)
        })

    return jsonify(detections)

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000)
