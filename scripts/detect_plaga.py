from flask import Flask, request, jsonify
from flask_cors import CORS
from ultralytics import YOLO
import os
from PIL import Image
import requests
import threading
import time

app = Flask(__name__)
CORS(app)

MODEL_PATH = os.path.join(os.path.dirname(__file__), 'my_model', 'train', 'weights', 'best.pt')

if not os.path.exists(MODEL_PATH):
    raise FileNotFoundError(f"Modelo no encontrado en: {MODEL_PATH}")

model = YOLO(MODEL_PATH)

# === Configura tu URL de Laravel ===
LARAVEL_API_URL = "http://localhost:8000/api/notificaciones"
TOKEN_SANCTUM = "PEGA_AQUI_TU_TOKEN"

# === Variable de control para evitar múltiples envíos por minuto ===
last_notification_time = 0

def enviar_notificacion(usuario_id, plaga_detectada):
    global last_notification_time

    now = time.time()
    if now - last_notification_time < 60:
        print("⏳ Notificación omitida, ya se envió una en el último minuto.")
        return

    payload = {
        "usuario_id": usuario_id,
        "mensaje": f"🚨 Se ha detectado una plaga: {plaga_detectada}"
    }
    headers = {
        "Authorization": f"Bearer {TOKEN_SANCTUM}",
        "Content-Type": "application/json"
    }

    try:
        response = requests.post(LARAVEL_API_URL, json=payload, headers=headers)
        print(f"✅ Notificación enviada: {response.status_code} - {response.text}")
        last_notification_time = now
    except Exception as e:
        print(f"❌ Error al enviar notificación a Laravel: {e}")

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
                nombre = model.names[cls]
                detections.append({
                    'class': nombre,
                    'confidence': round(conf, 2)
                })

        # Solo si hay detecciones, lanza la notificación
        if detections:
            threading.Thread(target=enviar_notificacion, args=(1, detections[0]['class'])).start()

        return jsonify({'detections': detections})

    except Exception as e:
        return jsonify({'error': f'Error al procesar la imagen: {str(e)}'}), 500

@app.route('/', methods=['GET'])
def home():
    return "<h1>Servidor de detección de plagas</h1><p>Endpoint: <code>/detect</code></p>"

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000)
