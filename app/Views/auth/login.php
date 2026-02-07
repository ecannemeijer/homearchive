<?php
/**
 * Login view
 */
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - Mijn Abonnementen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-500 to-blue-700 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <i class="fas fa-wallet text-blue-600 text-4xl mb-2"></i>
                <h1 class="text-3xl font-bold text-gray-800">Mijn Abonnementen</h1>
                <p class="text-gray-600 text-sm mt-2">Beheer al uw abonnementen en verzekeringen</p>
            </div>

            <!-- Flash messages -->
            <?php if (!empty($flash)): ?>
            <div class="mb-4 p-4 rounded <?php echo $flash['type'] === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                <p><?php echo esc_html($flash['message']); ?></p>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="/login" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mailadres</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="u@example.com"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Wachtwoord</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="••••••••"
                    >
                </div>

                <button 
                    type="submit"
                    class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i> Inloggen
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center my-6">
                <div class="flex-1 border-t border-gray-300"></div>
                <div class="px-3 text-gray-500 text-sm">of</div>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            <!-- Signup link -->
            <p class="text-center text-gray-600 text-sm">
                Nog geen account?
                <a href="/register" class="text-blue-600 font-semibold hover:text-blue-700">Registreer hier</a>
            </p>
        </div>
    </div>
</body>
</html>
