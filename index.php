<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Slots</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://source.unsplash.com/random/1920x1080') no-repeat center center fixed;
            background-size: cover;
            color: white;
            text-align: center;
        }
        .container {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 10px;
            max-width: 90%;
            margin: 50px auto;
        }
        .slot {
            padding: 20px;
            background-color: #444;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            user-select: none;
        }
        .slot.selected {
            background-color: green;
        }
        .slot.booked {
            background-color: red;
            pointer-events: none;
        }
        .summary {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Booking Slots</h1>
    <div class="container" id="slotsContainer"></div>
    <div class="summary">
        <h2>Total Slots Booked: <span id="totalBooked">0</span></h2>
        <h2>Total Amount: ₹<span id="totalAmount">0</span></h2>
    </div>

    <!-- Firebase Configuration -->
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-auth.js"></script>

    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-database.js"></script>
    <script>
        // Your Firebase Configuration
        const firebaseConfig = {
            apiKey: "YOUR_API_KEY",
            authDomain: "YOUR_AUTH_DOMAIN",
            databaseURL: "YOUR_DATABASE_URL",
            projectId: "YOUR_PROJECT_ID",
            storageBucket: "YOUR_STORAGE_BUCKET",
            messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
            appId: "YOUR_APP_ID"
        };

        // Initialize Firebase
        const app = firebase.initializeApp(firebaseConfig);
        const db = firebase.database();

        const slotsContainer = document.getElementById('slotsContainer');
        const totalBooked = document.getElementById('totalBooked');
        const totalAmount = document.getElementById('totalAmount');
        const slotPrice = 3000;
        const auth = firebase.auth();
const provider = new firebase.auth.GoogleAuthProvider();

function signIn() {
  auth.signInWithPopup(provider)
    .then(result => {
      console.log("User signed in: ", result.user);
      // Proceed to display the slots
    })
    .catch(error => {
      console.error("Sign In Error", error);
    });
}

function signOut() {
  auth.signOut().then(() => {
    console.log("User signed out");
  });
}

        // Create 500 Slots
        for (let i = 1; i <= 500; i++) {
            const slot = document.createElement('div');
            slot.classList.add('slot');
            slot.textContent = i;
            slotsContainer.appendChild(slot);

            // Click to select
            slot.addEventListener('click', () => {
                if (!slot.classList.contains('booked')) {
                    slot.classList.toggle('selected');
                    updateSummary();
                }
            });

            // Long press to remove
            let pressTimer;
            slot.addEventListener('mousedown', () => {
                pressTimer = setTimeout(() => {
                    slot.classList.remove('selected');
                    updateSummary();
                }, 800);
            });
            slot.addEventListener('mouseup', () => clearTimeout(pressTimer));
            slot.addEventListener('mouseleave', () => clearTimeout(pressTimer));
        }

        // Update Summary
        function updateSummary() {
            const selectedSlots = document.querySelectorAll('.selected');
            totalBooked.textContent = selectedSlots.length;
            totalAmount.textContent = selectedSlots.length * slotPrice;
        }

        // Sync with Firebase
        function syncWithFirebase() {
            firebase.database().ref('bookedSlots').on('value', (snapshot) => {
                const bookedSlots = snapshot.val() || {};
                document.querySelectorAll('.slot').forEach(slot => {
                    const slotNumber = slot.textContent;
                    if (bookedSlots[slotNumber]) {
                        slot.classList.add('booked');
                        slot.classList.remove('selected');
                    } else {
                        slot.classList.remove('booked');
                    }
                });
                updateSummary();
            });
        }
        syncWithFirebase();

        // Save to Firebase
        window.addEventListener('beforeunload', () => {
            const selectedSlots = document.querySelectorAll('.selected');
            selectedSlots.forEach(slot => {
                const slotNumber = slot.textContent;
                firebase.database().ref(`bookedSlots/${slotNumber}`).set(true);
                slot.classList.remove('selected');
            });
        });
    </script>
    <button onclick="signIn()">Sign In with Google</button>
    <button onclick="signOut()">Sign Out</button>
    
</body>
</html>
