from flask import Flask, request, jsonify
from flask_cors import CORS 
import requests

app = Flask(__name__)
# CORS aktif agar PHP di localhost:80 bisa akses Flask di localhost:5000
CORS(app)

# Konfigurasi Ollama Lokal
OLLAMA_URL = "http://localhost:11434/api/generate"
# Tetap pakai Llama 3 (8B) sesuai permintaanmu
MODEL_NAME = "llama3" 

@app.route('/chat', methods=['POST'])
def chat():
    try:
        # 1. Ambil data JSON dari Frontend (PHP/Postman)
        data = request.get_json()
        if not data:
            return jsonify({"status": "error", "message": "JSON tidak valid!"}), 400
            
        user_message = data.get("message")

        if not user_message:
            return jsonify({"status": "error", "message": "Pesan kosong!"}), 400

        # 2. Siapkan Payload untuk Ollama
        payload = {
            "model": MODEL_NAME,
            "prompt": f"Kamu adalah guide wisata Indonesia yang ramah. Jawab pertanyaan ini dengan detail: {user_message}",
            "stream": False 
        }

        # 3. Kirim ke Ollama
        # Ditambahkan timeout=180 (3 menit) karena Llama 3 berat di CPU
        response = requests.post(OLLAMA_URL, json=payload, timeout=180)
        
        if response.status_code == 200:
            ai_reply = response.json().get("response")
            return jsonify({
                "status": "success",
                "reply": ai_reply.strip()
            })
        else:
            return jsonify({
                "status": "error", 
                "message": f"Ollama Error: {response.status_code}. Pastikan model '{MODEL_NAME}' sudah di-download."
            }), 500

    except requests.exceptions.Timeout:
        return jsonify({"status": "error", "message": "Waktu tunggu habis (Timeout). AI terlalu lama berpikir."}), 504
    except requests.exceptions.ConnectionError:
        return jsonify({"status": "error", "message": "Gagal terhubung ke Ollama. Pastikan aplikasi Ollama sudah terbuka."}), 503
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    print(f"-------------------------------------------")
    print(f"SERVER CHATBOT WISATA (LLAMA 3) AKTIF")
    print(f"Menunggu request di http://localhost:5000")
    print(f"-------------------------------------------")
    app.run(host='0.0.0.0', port=5000, debug=True)
