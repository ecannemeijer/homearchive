<?php
/**
 * Create/Edit Password view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Nieuw wachtwoord toevoegen</h1>
        <p class="text-gray-600">Wachtwoorden zijn versleuteld opgeslagen</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="/password-vault" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
                <input type="text" id="title" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="bijv. Gmail">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Gebruikersnaam/Email</label>
                    <input type="text" id="username" name="username" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="u@example.com">
                </div>
                <div>
                    <label for="website_url" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" id="website_url" name="website_url" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://example.com">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Wachtwoord *</label>
                <div class="flex gap-2">
                    <input type="password" id="password" name="password" required class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="••••••••">
                    <button type="button" id="toggle-password" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" id="generate-btn" class="px-4 py-2 bg-green-200 rounded-lg hover:bg-green-300">
                        <i class="fas fa-random"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-600 mt-1">
                    <i class="fas fa-shield-alt mr-1"></i> Sterkte: <span id="pwd-strength">-</span>
                </p>
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                <input type="text" id="tags" name="tags" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="werk, streaming, email, ...">
                <p class="text-xs text-gray-600 mt-1">Kommaseparated</p>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
                <textarea id="notes" name="notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Aanvullende info..."></textarea>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-600 flex items-start">
                    <i class="fas fa-info-circle mr-2 mt-0.5 flex-shrink-0"></i>
                    <span>Uw wachtwoorden worden versleuteld opgeslagen met AES-256-CBC encryptie. Zelfs we beheerders kunnen deze niet zien.</span>
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i> Opslaan
                </button>
                <a href="/password-vault" class="flex-1 bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg hover:bg-gray-400 transition text-center">
                    <i class="fas fa-times mr-2"></i> Annuleren
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('toggle-password').addEventListener('click', function() {
        const input = document.getElementById('password');
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Generate password
    document.getElementById('generate-btn').addEventListener('click', function() {
        if (!confirm('Uw wachtwoord vervangen?')) return;
        
        fetch('/api/password/generate?length=16').then(r => r.json()).then(data => {
            document.getElementById('password').value = data.password;
            document.getElementById('password').type = 'text';
            updateStrength();
        });
    });

    // Password strength
    function updateStrength() {
        const pwd = document.getElementById('password').value;
        const display = document.getElementById('pwd-strength');
        
        let strength = 0;
        if (pwd.length >= 8) strength++;
        if (pwd.length >= 12) strength++;
        if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength++;
        if (/[0-9]/.test(pwd)) strength++;
        if (/[^a-zA-Z0-9]/.test(pwd)) strength++;

        const strengths = ['Zwak', 'Matig', 'Goed', 'Zeer goed', 'Sterke'];
        const colors = ['red', 'orange', 'yellow', 'blue', 'green'];
        
        display.textContent = strengths[Math.min(strength, 4)];
        display.className = 'text-' + colors[Math.min(strength, 4)] + '-600 font-semibold';
    }

    document.getElementById('password').addEventListener('input', updateStrength);
</script>
