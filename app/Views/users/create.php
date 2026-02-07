<?php
/**
 * Create User view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Nieuwe gebruiker toevoegen</h1>
        <p class="text-gray-600">Vul hieronder de gegevens in</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="/users" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Naam *</label>
                <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Voornaam Achternaam">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mailadres *</label>
                <input type="email" id="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="user@example.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Wachtwoord *</label>
                <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Minstens 6 karakters">
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_admin" value="1" class="w-4 h-4 rounded border-gray-300 focus:ring-2 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Beheerder (kan gebruikers beheren)</span>
                </label>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i> Opslaan
                </button>
                <a href="/users" class="flex-1 bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg hover:bg-gray-400 transition text-center">
                    <i class="fas fa-times mr-2"></i> Annuleren
                </a>
            </div>
        </form>
    </div>
</div>
