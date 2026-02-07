<?php
/**
 * Costs Overview view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Kosten Overzicht</h1>
        <p class="text-gray-600">Analyseer uw abonnementen- en verzekeringkosten</p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Maandelijkse kosten</p>
            <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo format_price($monthly_total); ?></p>
            <p class="text-xs text-gray-600 mt-2">Dit maand</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Jaarlijkse kosten</p>
            <p class="text-3xl font-bold text-green-600 mt-2"><?php echo format_price($yearly_total); ?></p>
            <p class="text-xs text-gray-600 mt-2">Per 12 maanden</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Abonnementen</p>
            <p class="text-3xl font-bold text-purple-600 mt-2"><?php echo format_price($subscription_cost); ?></p>
            <p class="text-xs text-gray-600 mt-2"><?php echo $subscription_count; ?> items</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Verzekeringen</p>
            <p class="text-3xl font-bold text-orange-600 mt-2"><?php echo format_price($insurance_cost); ?></p>
            <p class="text-xs text-gray-600 mt-2"><?php echo $insurance_count; ?> items</p>
        </div>
    </div>

    <!-- Export -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-download mr-2 text-blue-600"></i> Export
        </h2>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="/costs/export/csv?type=all" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-block text-center">
                <i class="fas fa-file-csv mr-2"></i> Alles (CSV)
            </a>
            <a href="/costs/export/csv?type=subscription" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-block text-center">
                <i class="fas fa-file-csv mr-2"></i> Alleen abonnementen (CSV)
            </a>
            <a href="/costs/export/csv?type=insurance" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 inline-block text-center">
                <i class="fas fa-file-csv mr-2"></i> Alleen verzekeringen (CSV)
            </a>
        </div>
    </div>

    <!-- Details Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b p-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-list mr-2 text-blue-600"></i> Alle items
            </h2>
        </div>

        <?php if (!empty($all_subs)): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Naam</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Maandelijks</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Jaarlijks</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Frequentie</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($all_subs as $sub): 
                        $monthly = $sub['frequency'] === 'yearly' ? $sub['cost'] / 12 : $sub['cost'];
                        $yearly = $sub['frequency'] === 'yearly' ? $sub['cost'] : $sub['cost'] * 12;
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo esc_html($sub['name']); ?></td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $sub['type'] === 'subscription' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                <?php echo type_label($sub['type']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?php echo format_price($monthly); ?></td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?php echo format_price($yearly); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo frequency_label($sub['frequency']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-8 text-center text-gray-600">
            <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
            <p>Geen items om toe te voegen</p>
        </div>
        <?php endif; ?>
    </div>
</div>
