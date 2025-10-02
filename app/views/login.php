<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi - SportEvents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'primary': '#2563eb',
                        'primary-dark': '#1d4ed8',
                        'secondary': '#f59e0b',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-inter bg-gray-50 min-h-screen flex flex-col">
    <!-- Includi navbar unificata -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <div class="flex-1 flex items-center justify-center py-12">
        <div class="max-w-md w-full mx-4">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Benvenuto</h1>
                    <p class="text-gray-600">Accedi al tuo account SportEvents</p>
                </div>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">⚠️</span>
                            <span class="text-red-700"><?= htmlspecialchars($_SESSION['error']) ?></span>
                        </div>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <span class="text-green-500 mr-2">✅</span>
                            <span class="text-green-700"><?= htmlspecialchars($_SESSION['success']) ?></span>
                        </div>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <form method="POST" action="/login" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                               required value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>"
                               placeholder="la-tua-email@esempio.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                               required placeholder="Inserisci la tua password">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700">Ricordami</span>
                        </label>
                        <a href="/forgot-password" class="text-sm text-primary hover:text-primary-dark transition">
                            Password dimenticata?
                        </a>
                    </div>

                    <button type="submit" class="w-full px-4 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition transform hover:scale-105">
                        🔑 Accedi
                    </button>
                </form>

                <!-- Separatore -->
                <div class="flex items-center my-6">
                    <div class="flex-1 h-px bg-gray-300"></div>
                    <span class="px-4 text-sm text-gray-500">oppure</span>
                    <div class="flex-1 h-px bg-gray-300"></div>
                </div>

                <!-- Login social (placeholder) -->
                <div class="space-y-3">
                    <button type="button" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                        <span class="mr-2">📧</span>
                        Continua con Google
                    </button>
                    <button type="button" class="w-full px-4 py-3 bg-blue-800 text-white rounded-lg font-medium hover:bg-blue-900 transition">
                        <span class="mr-2">📘</span>
                        Continua con Facebook
                    </button>
                </div>
            </div>

            <div class="text-center mt-6">
                <p class="text-gray-600">
                    Non hai ancora un account? 
                    <a href="/register" class="text-primary font-semibold hover:text-primary-dark transition">Registrati gratuitamente</a>
                </p>
            </div>
        </div>

        <!-- Demo credentials info -->
        <div class="mt-8 max-w-md w-full mx-4">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-3 text-blue-900 flex items-center">
                    <span class="mr-2">🚀</span>
                    Credenziali Demo per Testing
                </h3>
                <div class="space-y-2 text-sm text-blue-800">
                    <div class="p-3 bg-white rounded-lg">
                        <div class="font-medium">Organizzatore:</div>
                        <div class="text-blue-600">organizer@example.com | password123</div>
                    </div>
                    <div class="p-3 bg-white rounded-lg">
                        <div class="font-medium">Partecipante:</div>
                        <div class="text-blue-600">participant@example.com | password123</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>

    <?php 
    // Pulisci i dati del form dalla sessione
    unset($_SESSION['form_data']); 
    ?>
</body>
</html>
