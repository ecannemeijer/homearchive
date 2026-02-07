<?php
/**
 * Offers index - admin only
 */
$page_title = 'Aanbiedingen Beheer';
require_once __DIR__ . '/../layout.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Aanbiedingen Beheer</h1>
    <a href="/offers/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-2"></i>Nieuwe Aanbieding
    </a>
</div>

<?php if (!empty($flash)): ?>
<div class="mb-4 p-4 rounded-lg <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
    <?= esc_html($flash['message']) ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <?php if (empty($offers)): ?>
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-inbox text-4xl mb-4"></i>
            <p>Nog geen aanbiedingen toegevoegd.</p>
            <a href="/offers/create" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                Voeg eerste aanbieding toe →
            </a>
        </div>
    <?php else: ?>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prijs</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequentie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($offers as $offer): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                        <?= esc_html($offer['provider']) ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <?= esc_html($offer['plan_name']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <?= esc_html($offer['category'] ?? '-') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                        €<?= number_format($offer['price'], 2) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <?= $offer['frequency'] === 'monthly' ? 'Maandelijks' : 'Jaarlijks' ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($offer['is_active']): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Actief
                            </span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Inactief
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="/offers/<?= $offer['id'] ?>/edit" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i> Bewerken
                        </a>
                        <form method="POST" action="/offers/<?= $offer['id'] ?>/delete" class="inline" onsubmit="return confirm('Weet je zeker dat je deze aanbieding wilt verwijderen?')">
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> Verwijderen
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Totaal: <strong><?= count($offers) ?></strong> aanbiedingen
            </p>
        </div>
    <?php endif; ?>
</div>
