<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Chatbot Wisata Offline</title>
    <style>
        /* CSS Biasa - Offline Friendly */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 500px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box; /* Biar gak luber */
            resize: vertical;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover {
            background-color: #0056b3;
        }
        button:disabled {
            background-color: #ccc;
        }
        #result {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 5px solid #007bff;
            display: none;
            line-height: 1.5;
        }
        .loading-text {
            display: none;
            text-align: center;
            color: #777;
            font-style: italic;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Chatbot Wisata</h1>
    
    <div class="form-group">
        <label>Pertanyaan:</label>
        <textarea id="msg" rows="4" placeholder="Ketik sesuatu..."></textarea>
    </div>
    
    <button id="sendBtn">KIRIM PERTANYAAN</button>
    
    <div id="loading" class="loading-text">Sabar ya, AI lagi mikir keras...</div>
    
    <div id="result">
        <strong>Jawaban:</strong>
        <p id="replyText"></p>
    </div>
</div>

<script>
    // JS Biasa - Tanpa Library Luar
    const btn = document.getElementById('sendBtn');
    const msgInput = document.getElementById('msg');
    const loading = document.getElementById('loading');
    const resultBox = document.getElementById('result');
    const replyText = document.getElementById('replyText');

    btn.onclick = async function() {
        const text = msgInput.value;
        if(!text) return alert("Isi dulu pertanyaannya!");

        // Loading state
        btn.disabled = true;
        loading.style.display = 'block';
        resultBox.style.display = 'none';

        try {
            const response = await fetch('http://192.168.100.6:5000/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: text })
            });

            const data = await response.json();
            
            if(data.status === 'success') {
                replyText.innerText = data.reply;
            } else {
                replyText.innerText = "Gagal: " + data.message;
            }
            resultBox.style.display = 'block';

        } catch (err) {
            alert("Cek Flask kamu! Pastikan sudah jalan.");
        } finally {
            btn.disabled = false;
            loading.style.display = 'none';
        }
    };
</script>

</body>
</html>
