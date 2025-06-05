from flask import Flask, request, jsonify
from flask_cors import CORS
from ultralytics import YOLO
import os
from PIL import Image

app = Flask(__name__)
CORS(app)

# Cargar el modelo YOLO desde la ruta relativa
MODEL_PATH = os.path.join(os.path.dirname(__file__), 'my_model', 'train', 'weights', 'best.pt')

# Verificar si el modelo existe
if not os.path.exists(MODEL_PATH):
    raise FileNotFoundError(f"Modelo no encontrado en: {MODEL_PATH}")

model = YOLO(MODEL_PATH)

@app.route('/', methods=['GET'])
def home():
    return "<h1>Servidor de detección de plagas</h1><p>Usa el endpoint <code>/detect</code> con método POST y una imagen.</p>"

@app.route('/detect', methods=['POST'])
def detect():
    if 'image' not in request.files:
        return jsonify({'error': 'No se ha enviado ninguna imagen'}), 400

    try:
        image_file = request.files['image']
        image = Image.open(image_file.stream).convert('RGB')

        results = model(image)

        detections = []
        for r in results:
            for box in r.boxes:
                cls = int(box.cls[0])
                conf = float(box.conf[0])
                detections.append({
                    'class': model.names[cls],
                    'confidence': round(conf, 2)
                })

        return jsonify({'detections': detections})

    except Exception as e:
        return jsonify({'error': f'Error al procesar la imagen: {str(e)}'}), 500

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000)
