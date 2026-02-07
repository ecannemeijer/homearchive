<?php
/**
 * Create offer - admin only
 */
$page_title = 'Nieuwe Aanbieding';
require_once __DIR__ . '/../layout.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="/offers" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Terug naar overzicht
        </a>
    </div>
    
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Nieuwe Aanbieding Toevoegen</h1>
    
    <?php if (!empty($flash)): ?>
    <div class="mb-4 p-4 rounded-lg <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
        <?= esc_html($flash['message']) ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="/offers" class="bg-white rounded-lg shadow p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="provider" class="block text-sm font-medium text-gray-700 mb-2">
                    Provider <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="provider" 
                    name="provider" 
                    required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Bijv. Ziggo, VGZ, Netflix"
                >
                <p class="mt-1 text-sm text-gray-500">Naam van de aanbieder</p>
            </div>
            
            <div>
                <label for="plan_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Plan Naam <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="plan_name" 
                    name="plan_name" 
                    required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Bijv. Start Pakket, Basis Dekking"
                >
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                    Prijs <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500">€</span>
                    <input 
                        type="number" 
                        step="0.01" 
                        id="price" 
                        name="price" 
                        required 
                        min="0"
                        class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="29.99"
                    >
                </div>
            </div>
            
            <div>
                <label for="frequency" class="block text-sm font-medium text-gray-700 mb-2">
                    Frequentie <span class="text-red-500">*</span>
                </label>
                <select 
                    id="frequency" 
                    name="frequency" 
                    required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="monthly">Maandelijks</option>
                    <option value="yearly">Jaarlijks</option>
                </select>
            </div>
        </div>
        
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                Categorie
            </label>
            <select 
                id="category" 
                name="category" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
                <option value="">-- Selecteer categorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= esc_attr($cat['name']) ?>"><?= esc_html($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Beschrijving
            </label>
            <textarea 
                id="description" 
                name="description" 
                rows="3" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Korte beschrijving van het aanbod..."
            ></textarea>
        </div>
        
        <div>
            <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
                Website URL
            </label>
            <input 
                type="url" 
                id="url" 
                name="url" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="https://www.provider.nl/aanbieding"
            >
            <p class="mt-1 text-sm text-gray-500">Link naar de aanbieding</p>
        </div>
        
        <div>
            <label for="conditions" class="block text-sm font-medium text-gray-700 mb-2">
                Voorwaarden
            </label>
            <textarea 
                id="conditions" 
                name="conditions" 
                rows="2" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Bijv. Alleen voor nieuwe klanten, 12 maanden contract..."
            ></textarea>
        </div>
        
        <div class="border-t pt-4">
            <label class="flex items-center">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    checked 
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <span class="ml-2 text-sm text-gray-700">
                    Actief (wordt getoond in prijsvergelijkingen)
                </span>
            </label>
        </div>
        
        <div class="flex gap-4 pt-4 border-t">
            <button 
                type="submit" 
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium"
            >
                <i class="fas fa-save mr-2"></i>Opslaan
            </button>
            <a 
                href="/offers" 
                class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition font-medium"
            >
                Annuleren
            </a>
        </div>
    </form>
</div>
