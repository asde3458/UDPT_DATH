from flask import Flask, request, jsonify
from flask_cors import CORS
from dotenv import load_dotenv
import os
from routes import prescription_routes

# Load environment variables
load_dotenv()

app = Flask(__name__)

# Cấu hình CORS đơn giản
CORS(app, origins=["http://localhost:8000", "http://127.0.0.1:8000"], 
     methods=["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"],
     allow_headers=["Content-Type", "Authorization"])

# Register routes
app.register_blueprint(prescription_routes, url_prefix='/api/prescriptions')

# Handle OPTIONS requests
@app.route('/api/prescriptions', methods=['OPTIONS'])
@app.route('/api/prescriptions/<path:path>', methods=['OPTIONS'])
def handle_options(path=None):
    response = jsonify({'status': 'ok'})
    response.headers.add('Access-Control-Allow-Origin', 'http://localhost:8000')
    response.headers.add('Access-Control-Allow-Headers', 'Content-Type,Authorization')
    response.headers.add('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS')
    return response

if __name__ == '__main__':
    port = int(os.environ.get('PORT', 3002))
    app.run(host='0.0.0.0', port=port, debug=True) 