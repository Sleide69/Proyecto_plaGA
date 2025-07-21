FROM python:3.11

WORKDIR /usr/src/app

# Instala dependencias del sistema necesarias para OpenCV
RUN apt-get update && apt-get install -y libgl1

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 5000

ENV FLASK_APP=my_model.servidor_flask

CMD ["flask", "run", "--host=0.0.0.0", "--port=5000"]