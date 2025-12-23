<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCM Push Notification Sender</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .loader {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        #statusMessage {
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
            opacity: 0;
            transform: translateY(10px);
        }

        #statusMessage.visible {
            opacity: 1;
            transform: translateY(0);
        }

        textarea {
            word-break: break-all;
        }
    </style>
</head>

<body
    class="bg-gray-900 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(120,119,198,0.3),rgba(255,255,255,0))] flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-lg mx-auto bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-700">
        <div class="p-8">
            <div class="text-center mb-6">
                <div class="inline-block bg-blue-500/20 p-3 rounded-xl mb-4">
                    <i class="ph ph-paper-plane-tilt text-3xl text-blue-400"></i>
                </div>
                <h1 class="text-3xl font-bold text-white">Push Notification Sender</h1>
                <p class="text-gray-400 mt-2">Generate a real token and send a notification.</p>
            </div>

            <!-- SETUP INSTRUCTIONS -->
            <div class="mb-6 p-4 bg-yellow-900/30 border border-yellow-500/50 text-yellow-300 rounded-lg text-sm">
                <h3 class="font-bold mb-2">Setup Required</h3>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Paste your `firebaseConfig` and `vapidKey` into the script tag at the bottom of this file.</li>
                    <li>Ensure your `firebase-messaging-sw.js` file is inside the `public` folder alongside this HTML
                        file.</li>
                </ol>
            </div>

            <div>
                <label for="token" class="block text-sm font-medium text-gray-300 mb-2">FCM Device
                    Token</label>
                <div class="flex items-center space-x-2">
                    <div class="relative w-full">
                        <i class="ph ph-key absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <textarea id="token" name="token" rows="3"
                            class="w-full pl-10 pr-4 py-2 text-white bg-gray-800/50 border border-gray-700 rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-gray-800 transition duration-150"
                            placeholder="Click 'Get Real Token' or paste one here..."></textarea>
                    </div>
                    <button type="button" id="getTokenButton"
                        class="flex-shrink-0 h-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Get Real Token
                    </button>
                </div>
            </div>

            <div id="statusMessage" class="mt-6 text-center text-sm font-medium"></div>
        </div>
    </div>

    <!-- Firebase SDK and Form Logic -->
    <script type="module">
        // STEP 1: Import Firebase SDK functions
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
        import {
            getMessaging,
            getToken
        } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";

        // STEP 2: PASTE YOUR FIREBASE CONFIGURATION HERE
        const firebaseConfig = {
            apiKey: "AIzaSyAHRdYjEG3k1JzYR7OW31bLfC71qi0UNCY",
            authDomain: "skywalker-notification.firebaseapp.com",
            projectId: "skywalker-notification",
            storageBucket: "skywalker-notification.firebasestorage.app",
            messagingSenderId: "624087602629",
            appId: "1:624087602629:web:e0bd6c7aaef5ccea2c27ac",
            measurementId: "G-QZWS5CXB81"
        };

        // STEP 3: PASTE YOUR VAPID KEY HERE
        const vapidKey = "BMP4uIYiZZxGFnZWbQR5Ak93lcODHEZedo8A19Lpm7CV3OG31oE5a6aSmF0c6XnFHAxbN0C19b2TWZv6aUaF8uA";

        // --- Initialize Firebase ---
        try {
            const app = initializeApp(firebaseConfig);
            var messaging = getMessaging(app);
        } catch (e) {
            console.error("Firebase initialization failed. Did you paste your config object?", e);
            showStatus("Firebase initialization failed. Please check your config.", "error");
        }

        // --- Get DOM Elements ---
        const tokenTextarea = document.getElementById('token');
        const getTokenButton = document.getElementById('getTokenButton');
        const statusMessage = document.getElementById('statusMessage');
        const loader = document.getElementById('loader');

        // --- Helper to display status messages ---
        function showStatus(message, type = 'error') {
            statusMessage.textContent = message;
            statusMessage.className = 'mt-6 text-center text-sm font-medium visible';
            if (type === 'success') statusMessage.classList.add('text-green-400');
            else if (type === 'info') statusMessage.classList.add('text-blue-400');
            else statusMessage.classList.add('text-red-400');
            setTimeout(() => statusMessage.classList.remove('visible'), 5000);
        }

        // --- Logic to get a real FCM token ---
        async function retrieveToken() {
            showStatus('Requesting notification permission...', 'info');
            try {
                if (!messaging) {
                    showStatus("Messaging not initialized. Check Firebase config.", "error");
                    return;
                }
                const swRegistration = await navigator.serviceWorker.register('./firebase.js');
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    showStatus('Permission granted. Fetching token...', 'info');
                    const currentToken = await getToken(messaging, {
                        vapidKey: vapidKey,
                        serviceWorkerRegistration: swRegistration
                    });
                    if (currentToken) {
                        tokenTextarea.value = currentToken;
                        showStatus('Real FCM token generated successfully!', 'success');
                    } else {
                        showStatus('Failed to get token. Ensure you are on HTTPS if not using localhost.', 'error');
                    }
                } else {
                    showStatus('Permission denied. Cannot get token.', 'error');
                }
            } catch (err) {
                console.error('Token retrieval error:', err);
                showStatus(`Error: ${err.message}`, 'error');
            }
        }
        getTokenButton.addEventListener('click', retrieveToken);
    </script>
</body>

</html>
