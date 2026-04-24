<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cheezi - Cheezious Voice Assistant</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .container {
            text-align: center;
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }

        .logo {
            font-size: 48px;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #ffd700;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #a0aec0;
            margin-bottom: 40px;
            font-size: 1rem;
        }

        .mic-btn {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
            font-size: 40px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 30px rgba(229, 62, 62, 0.4);
            margin-bottom: 20px;
        }

        .mic-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 50px rgba(229, 62, 62, 0.7);
        }

        .mic-btn.active {
            background: linear-gradient(135deg, #48bb78, #38a169);
            box-shadow: 0 0 50px rgba(72, 187, 120, 0.7);
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }

        .status {
            font-size: 1.1rem;
            color: #e2e8f0;
            margin-bottom: 30px;
            min-height: 30px;
        }

        .status.listening { color: #68d391; }
        .status.speaking  { color: #fbd38d; }
        .status.error     { color: #fc8181; }

        .transcript-box {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 20px;
            min-height: 100px;
            text-align: left;
            font-size: 0.95rem;
            line-height: 1.6;
            color: #e2e8f0;
            margin-bottom: 20px;
        }

        .brand {
            color: #718096;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">🍕</div>
    <h1>Cheezi</h1>
    <p class="subtitle">Cheezious Voice Assistant — Pakistan ka Apna Food Buddy</p>

    <button class="mic-btn" id="micBtn" onclick="toggleCall()">🎤</button>

    <div class="status" id="status">Tap to talk with Cheezi</div>

    <div class="transcript-box" id="transcript">
        Your conversation will appear here...
    </div>

    <p class="brand">Powered by Cheezious × VAPI AI</p>
</div>

<script type="module">
    import Vapi from 'https://esm.sh/@vapi-ai/web@2.5.2';

    const vapiKey     = "{{ $vapiKey }}";
    const assistantId = "{{ $assistantId }}";

    const vapi   = new Vapi(vapiKey);
    let calling  = false;

    const btn        = document.getElementById('micBtn');
    const status     = document.getElementById('status');
    const transcript = document.getElementById('transcript');

    // Expose to window so onclick="toggleCall()" works inside type="module"
    window.toggleCall = toggleCall;

    function toggleCall() {
        if (!calling) {
            startCall();
        } else {
            stopCall();
        }
    }

    function startCall() {
        calling = true;
        btn.classList.add('active');
        btn.innerHTML = '⏹️';
        status.textContent = 'Connecting to Cheezi...';
        status.className = 'status';
        transcript.innerHTML = '';

        vapi.start(assistantId);
    }

    function stopCall() {
        calling = false;
        btn.classList.remove('active');
        btn.innerHTML = '🎤';
        status.textContent = 'Tap to talk with Cheezi';
        status.className = 'status';
        vapi.stop();
    }

    vapi.on('call-start', () => {
        status.textContent = '🟢 Connected — Listening...';
        status.className = 'status listening';
    });

    vapi.on('call-end', () => {
        calling = false;
        btn.classList.remove('active');
        btn.innerHTML = '🎤';
        status.textContent = 'Call ended. Tap to start again.';
        status.className = 'status';
    });

    vapi.on('speech-start', () => {
        status.textContent = '🟡 Cheezi is speaking...';
        status.className = 'status speaking';
    });

    vapi.on('speech-end', () => {
        status.textContent = '🟢 Listening...';
        status.className = 'status listening';
    });

    vapi.on('message', (msg) => {
        if (msg.type === 'transcript') {
            const role = msg.role === 'user' ? '🧑 You' : '🍕 Cheezi';
            transcript.innerHTML += `<p><strong>${role}:</strong> ${msg.transcript}</p><br>`;
            transcript.scrollTop = transcript.scrollHeight;
        }
    });

    vapi.on('error', (e) => {
        status.textContent = '❌ Error: ' + (e.message || 'Something went wrong');
        status.className = 'status error';
        stopCall();
    });
</script>
</body>
</html>
