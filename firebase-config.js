import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "AIzaSyAlcMzRwy4G2uc3CeKXvAj3HZdm1xGYjI0",
  authDomain: "telefonia-d8f6d.firebaseapp.com",
  databaseURL: "https://telefonia-d8f6d-default-rtdb.firebaseio.com",
  projectId: "telefonia-d8f6d",
  storageBucket: "telefonia-d8f6d.firebasestorage.app",
  messagingSenderId: "842673328033",
  appId: "1:842673328033:web:854243bd09dadd45ca85e0",
  measurementId: "G-0SZ1FLVVDG"
};


const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);

export { app, auth, db };
