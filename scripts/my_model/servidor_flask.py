from flask import Flask, request, jsonify
from flask_cors import CORS
import torch
from PIL import Image, UnidentifiedImageError
import io
import os
from ultralytics import YOLO # ¡Asegúrate de que esta línea esté presente!

app = Flask(__name__)
CORS(app)

# Cargar el modelo YOLO
try:
    # Cargar el modelo YOLO de tu archivo 'best.pt' directamente con la clase YOLO
    model = YOLO('C:/xampp/htdocs/Proyecto_plaGA/scripts/my_model/train/weights/best.pt')
    print("¡Modelo cargado exitosamente!")
except Exception as e:
    model = None
    print(f"Error al cargar el modelo: {e}. Asegúrate de que la ruta al archivo 'best.pt' es correcta y el archivo no está corrupto.")

@app.route('/', methods=['GET'])
def home():
    return jsonify({'message': 'Servidor Flask funcionando correctamente'})

@app.route('/detect', methods=['POST'])
def detect():
    if not model:
        return jsonify({'error': 'El modelo no se cargó correctamente en el servidor. Contacta al administrador.'}), 500

    if 'image' not in request.files:
        return jsonify({'error': 'No se envió una imagen en la solicitud'}), 400

    try:
        # Leer imagen
        image_file = request.files['image']
        img = Image.open(image_file.stream).convert('RGB') # Asegúrate de que la imagen sea RGB
    except UnidentifiedImageError:
        return jsonify({'error': 'El archivo enviado no es una imagen válida o está dañado'}), 400
    except Exception as e:
        return jsonify({'error': f'Error al abrir la imagen: {e}'}), 400

    try:
        # Procesar con YOLO
        # El método predict de la clase YOLO es la forma recomendada
        # 'source=img' le indica al modelo que use la imagen cargada
        # 'save=False' evita que guarde las imágenes con detecciones
        # 'conf=0.25' es el umbral de confianza, puedes ajustarlo
        results = model.predict(source=img, save=False, conf=0.25)

        # Extraer resultados
        detections = []
        # Itera sobre los resultados de cada imagen (aunque solo envíes una)
        for r in results:
            # Itera sobre las detecciones en cada resultado
            for box in r.boxes:
                class_id = int(box.cls)
                confidence = float(box.conf)
                # Opcional: Si necesitas las coordenadas de la caja delimitadora (bbox)
                # x1, y1, x2, y2 = box.xyxy[0].tolist()

                detections.append({
                    "name": model.names[class_id],
                    "confidence": confidence
                    # "bbox": [x1, y1, x2, y2] # Descomenta si necesitas las coordenadas
                })

        return jsonify(detections)
    except Exception as e:
        return jsonify({'error': f'Error al procesar la imagen con el modelo: {e}'}), 500

if __name__ == '__main__':
    # La advertencia es normal para entornos de desarrollo.
    # Para producción, usa un servidor WSGI como Gunicorn o Waitress.
    app.run(host='127.0.0.1', port=5000)