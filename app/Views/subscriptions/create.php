<?php
/**
 * Create Subscription view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Nieuw abonnement toevoegen</h1>
        <p class="text-gray-600">Vul hieronder alle gegevens in</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="/subscriptions" class="space-y-6">
            <!-- Basic Info -->
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Basisgegevens</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Naam *</label>
                        <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="bijv. Netflix">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                        <select id="type" name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="subscription">Abonnement</option>
                            <option value="insurance">Verzekering</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-1">Bedrag (€) *</label>
                        <input type="number" id="cost" name="cost" required step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="9.99">
                    </div>
                    <div>
                        <label for="frequency" class="block text-sm font-medium text-gray-700 mb-1">Frequentie *</label>
                        <select id="frequency" name="frequency" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="monthly">Maandelijks</option>
                            <option value="yearly">Jaarlijks</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Categorie</label>
                        <select id="category" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Geen categorie --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo esc_attr($cat['name']); ?>"><?php echo esc_html($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="billing_date" class="block text-sm font-medium text-gray-700 mb-1">Factureringsdag (1-31)</label>
                        <input type="number" id="billing_date" name="billing_date" min="1" max="31" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="1">
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Datums</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Startdatum</label>
                        <input type="date" id="start_date" name="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Einddatum</label>
                        <input type="date" id="end_date" name="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label for="renewal_reminder" class="block text-sm font-medium text-gray-700 mb-1">Herinnering voor verloop (dagen)</label>
                    <input type="number" id="renewal_reminder" name="renewal_reminder" value="7" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="7">
                </div>
            </div>

            <!-- Login Credentials -->
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Inloggegevens</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="website_url" class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                        <input type="url" id="website_url" name="website_url" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://example.com">
                    </div>
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Gebruikersnaam/Email</label>
                        <input type="text" id="username" name="username" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Wachtwoord</label>
                    <div class="flex gap-2">
                        <input type="password" id="password" name="password" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <button type="button" id="toggle-password" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Aanvullende informatie</h2>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Opmerkingen</label>
                    <textarea id="notes" name="notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Voeg hier opmerkingen toe..."></textarea>
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-gray-300 focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Dit abonnement is actief</span>
                    </label>
                </div>

                <div class="mt-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_monthly_cancelable" value="1" class="w-4 h-4 rounded border-gray-300 focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Maandelijks opzegbaar (geen einddatum nodig)</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 p-6 rounded-lg flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i> Opslaan
                </button>
                <a href="/subscriptions" class="flex-1 bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg hover:bg-gray-400 transition text-center">
                    <i class="fas fa-times mr-2"></i> Annuleren
                </a>
            </div>
        </form>
    </div>
</div>

<script>
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
</script>
