<?php
/**
 * Change Password view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Wachtwoord wijzigen</h1>
        <p class="text-gray-600">Update uw wachtwoord</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="/change-password" class="space-y-6">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Huidlig wachtwoord *</label>
                <input type="password" id="current_password" name="current_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Uw huidge wachtwoord">
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Nieuw wachtwoord *</label>
                <input type="password" id="new_password" name="new_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Minstens 6 karakters">
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Bevestig nieuw wachtwoord *</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Herhaal nieuw wachtwoord">
            </div>

            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-circle mr-2"></i> Zorg ervoor dat u het nieuwe wachtwoord onthoud, want u zult het nodig hebben om in te loggen
                </p>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i> Wachtwoord wijzigen
                </button>
                <a href="/dashboard" class="flex-1 bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg hover:bg-gray-400 transition text-center">
                    <i class="fas fa-times mr-2"></i> Annuleren
                </a>
            </div>
        </form>
    </div>
</div>
