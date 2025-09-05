<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System - Auth</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6366f1',
                        secondary: '#4f46e5',
                        accent: '#a5b4fc',
                        darkBackground: '#0f172a',
                        darkSurface: '#1e293b',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
        }

        .card {
            @apply bg-darkSurface rounded-2xl shadow-2xl p-8 w-full max-w-md relative transition-all duration-500 ease-in-out;
        }

        .hidden-view {
            opacity: 0;
            pointer-events: none;
            transform: translateY(20px);
            position: absolute;
        }

        .active-view {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
            position: relative;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4">

    <!-- CARD CONTAINER -->
    <div class="card text-center">

        <!-- LOGIN -->
        <div id="login-view" class="active-view">
            <h2 class="text-3xl font-bold text-white mb-6">Log In</h2>
            <form id="login-form" class="space-y-5" method="POST" action="core/user_handle.php">
                <input type="hidden" name="action" value="login">
                <input type="email" name="email" placeholder="Email"
                    class="w-full px-4 py-3 rounded-2xl bg-gray-800 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-primary"
                    required>
                <input type="password" name="password" placeholder="Password"
                    class="w-full px-4 py-3 rounded-2xl bg-gray-800 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-primary"
                    required>
                <button type="submit"
                    class="w-full bg-gradient-to-r from-primary to-secondary text-white font-bold py-3 rounded-2xl shadow-lg transform hover:scale-105 transition-all">
                    Log In
                </button>
            </form>

            <p class="mt-6 text-sm text-gray-400">Don't have an account?
                <a href="#" id="show-register" class="text-accent hover:underline font-medium">Register here</a>
            </p>
        </div>

        <!-- REGISTER -->
        <div id="register-view" class="hidden-view">
            <h2 class="text-3xl font-bold text-white mb-6">Register</h2>
            <form id="register-form" class="space-y-5" method="POST" action="core/user_handle.php">
                <input type="hidden" name="action" value="register">
                <input type="text" name="name" placeholder="Full Name"
                    class="w-full px-4 py-3 rounded-2xl bg-gray-800 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-primary"
                    required>
                <input type="email" name="email" placeholder="Email"
                    class="w-full px-4 py-3 rounded-2xl bg-gray-800 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-primary"
                    required>
                <input type="password" name="password" placeholder="Password"
                    class="w-full px-4 py-3 rounded-2xl bg-gray-800 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-primary"
                    required>
                <select name="role"
                    class="w-full px-4 py-3 rounded-2xl bg-gray-800 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit"
                    class="w-full bg-gradient-to-r from-primary to-secondary text-white font-bold py-3 rounded-2xl shadow-lg transform hover:scale-105 transition-all">
                    Register
                </button>
            </form>

            <p class="mt-6 text-sm text-gray-400">Already have an account?
                <a href="#" id="show-login" class="text-accent hover:underline font-medium">Log In</a>
            </p>
        </div>

    </div>

    <script>
        const loginView = document.getElementById("login-view");
        const registerView = document.getElementById("register-view");
        const showRegister = document.getElementById("show-register");
        const showLogin = document.getElementById("show-login");

        function toggleView(show, hide) {
            hide.classList.remove("active-view");
            hide.classList.add("hidden-view");

            setTimeout(() => {
                show.classList.remove("hidden-view");
                show.classList.add("active-view");
            }, 100);
        }

        showRegister.addEventListener("click", (e) => {
            e.preventDefault();
            toggleView(registerView, loginView);
        });

        showLogin.addEventListener("click", (e) => {
            e.preventDefault();
            toggleView(loginView, registerView);
        });
    </script>

</body>

</html>