// firebase-messaging-sw.js
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

const firebaseConfig = {
  apiKey: "AIzaSyAHRdYjEG3k1JzYR7OW31bLfC71qi0UNCY",
  authDomain: "skywalker-notification.firebaseapp.com",
  projectId: "skywalker-notification",
  storageBucket: "skywalker-notification.firebasestorage.app",
  messagingSenderId: "624087602629",
  appId: "1:624087602629:web:e0bd6c7aaef5ccea2c27ac",
  measurementId: "G-QZWS5CXB81"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: '/firebase-logo.png'
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
