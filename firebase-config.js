import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "AIzaSyAlcMzRwy4G2uc3CeKXvAj3HZdm1xGYjI0",
  authDomain: "telefonia-d8f6d.firebaseapp.com",
  projectId: "telefonia-d8f6d",
  storageBucket: "telefonia-d8f6d.firebasestorage.app",
  messagingSenderId: "842673328033",
  appId: "1:842673328033:web:5a018f68e50ccc6cca85e0",
  measurementId: "G-0V7GFZY49H"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);

export { app, auth, db };