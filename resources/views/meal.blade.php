<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSnap Food Management</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .section { margin-bottom: 20px; }
        .error { color: red; }
        .success { color: green; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
        input, button { margin: 5px; padding: 8px; }
        input[type="text"], input[type="number"], input[type="email"], input[type="password"] { width: 250px; }
        button { cursor: pointer; }
    </style>
</head>
<body>
    <div class="section">
        <h2>Login</h2>
        <input type="email" id="email" value="ceze46321@gmail.com" placeholder="Email">
        <input type="password" id="password" value="versatile" placeholder="Password">
        <button onclick="login()">Login</button>
        <p id="login-result"></p>
    </div>

    <div class="section">
        <h2>List Foods</h2>
        <button onclick="listFoods()">Get All Foods</button>
        <p id="foods-result"></p>
    </div>

    <div class="section">
        <h2>Get Food Details</h2>
        <input type="number" id="food_id" placeholder="Food ID">
        <button onclick="getFood()">Get Food</button>
        <p id="food-result"></p>
    </div>

    <div class="section">
        <h2>Add New Food (Admin Only)</h2>
        <input type="text" id="food_name" placeholder="Food Name (e.g., Chicken)">
        <input type="number" id="calories_per_100g" placeholder="Calories per 100g (e.g., 165)">
        <input type="text" id="nutrients" placeholder='{"sodium": 70, "sugar": 0, "protein": 31}'>
        <button onclick="addFood()">Add Food</button>
        <p id="add-food-result"></p>
    </div>

    <script>
        let token = null;

        async function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const resultEl = document.getElementById('login-result');
            try {
                const response = await fetch('https://bincone.apexjets.org/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                const data = await response.json();
                if (response.ok) {
                    token = data.token;
                    resultEl.innerHTML = `<span class="success">Login successful! Token: ${token}</span>`;
                } else {
                    resultEl.innerHTML = `<span class="error">Error: ${data.message}</span>`;
                }
            } catch (error) {
                resultEl.innerHTML = `<span class="error">Error: ${error.message}</span>`;
            }
        }

        async function listFoods() {
            if (!token) {
                document.getElementById('foods-result').innerHTML = '<span class="error">Please login first</span>';
                return;
            }
            const resultEl = document.getElementById('foods-result');
            try {
                const response = await fetch('https://bincone.apexjets.org/api/foods', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (response.ok) {
                    resultEl.innerHTML = `<span class="success">Success</span><pre>${JSON.stringify(data, null, 2)}</pre>`;
                } else {
                    resultEl.innerHTML = `<span class="error">Error: ${data.error || data.message}</span>`;
                }
            } catch (error) {
                resultEl.innerHTML = `<span class="error">Error: ${error.message}</span>`;
            }
        }

        async function getFood() {
            if (!token) {
                document.getElementById('food-result').innerHTML = '<span class="error">Please login first</span>';
                return;
            }
            const food_id = document.getElementById('food_id').value;
            const resultEl = document.getElementById('food-result');
            try {
                const response = await fetch(`https://bincone.apexjets.org/api/foods/${food_id}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (response.ok) {
                    resultEl.innerHTML = `<span class="success">Success</span><pre>${JSON.stringify(data, null, 2)}</pre>`;
                } else {
                    resultEl.innerHTML = `<span class="error">Error: ${data.error || data.message}</span>`;
                }
            } catch (error) {
                resultEl.innerHTML = `<span class="error">Error: ${error.message}</span>`;
            }
        }

        async function addFood() {
            if (!token) {
                document.getElementById('add-food-result').innerHTML = '<span class="error">Please login first</span>';
                return;
            }
            const name = document.getElementById('food_name').value;
            const calories_per_100g = document.getElementById('calories_per_100g').value;
            const nutrients = document.getElementById('nutrients').value;
            const resultEl = document.getElementById('add-food-result');
            try {
                const response = await fetch('https://bincone.apexjets.org/api/foods', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, calories_per_100g, nutrients })
                });
                const data = await response.json();
                if (response.ok) {
                    resultEl.innerHTML = `<span class="success">${data.message}</span><pre>${JSON.stringify(data.food, null, 2)}</pre>`;
                } else {
                    resultEl.innerHTML = `<span class="error">Error: ${data.error || data.message}</span>`;
                }
            } catch (error) {
                resultEl.innerHTML = `<span class="error">Error: ${error.message}</span>`;
            }
        }
    </script>
</body>
</html>