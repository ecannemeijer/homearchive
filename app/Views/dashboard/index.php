<?php
/**
 * Dashboard view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Dashboard</h1>
        <p class="text-gray-600">Welkom terug! Hier is uw abonnementenoverzicht.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Monthly -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Maandelijkse kosten</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo format_price($monthly_total); ?></p>
                </div>
                <div class="text-blue-500 text-3xl">
                    <i class="fas fa-credit-card"></i>
                </div>
            </div>
        </div>

        <!-- Total Yearly -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Jaarlijkse kosten</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo format_price($yearly_total); ?></p>
                </div>
                <div class="text-green-500 text-3xl">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>

        <!-- Total Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Alle items</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $all_subs; ?></p>
                </div>
                <div class="text-purple-500 text-3xl">
                    <i class="fas fa-cube"></i>
                </div>
            </div>
        </div>

        <!-- Active Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Actieve items</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $active_subs; ?></p>
                </div>
                <div class="text-orange-500 text-3xl">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Soon Warning -->
    <?php if (!empty($expiring)): ?>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-8">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 mt-1"></i>
            <div class="flex-1">
                <h3 class="font-bold text-yellow-800 mb-2">Items die bijna verlopen</h3>
                <div class="space-y-1 text-sm text-yellow-700">
                    <?php foreach ($expiring as $item): 
                        $days = days_until($item['end_date']);
                    ?>
                    <div class="flex justify-between">
                        <span><?php echo esc_html($item['name']); ?></span>
                        <span class="font-semibold">
                            <?php echo $days === 0 ? 'Verloopt vandaag' : $days . ' dag' . ($days !== 1 ? 'en' : ''); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Items & Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="border-b p-6">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-clock mr-2 text-blue-600"></i> Recente toevoegingen
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <?php if (!empty($recent)): ?>
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Naam</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Type</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Bedrag</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actie</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($recent as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo esc_html($item['name']); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $item['type'] === 'subscription' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                        <?php echo type_label($item['type']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?php echo format_price($item['cost']); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="/subscriptions/<?php echo $item['id']; ?>" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="p-6 text-center text-gray-600">
                        <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                        <p>Geen items gevonden</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Cost Breakdown -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b p-6">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-pie-chart mr-2 text-purple-600"></i> Kosten per type
                </h2>
            </div>
            <div class="p-6">
                <?php if (!empty($costs_by_type)): ?>
                <div class="space-y-4">
                    <?php foreach ($costs_by_type as $cost): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="w-3 h-3 rounded-full mr-3 <?php echo $cost['type'] === 'subscription' ? 'bg-blue-500' : 'bg-green-500'; ?>"></div>
                            <span class="text-sm text-gray-600"><?php echo type_label($cost['type']); ?></span>
                            <span class="text-xs text-gray-500 ml-2">(<?php echo $cost['count']; ?>)</span>
                        </div>
                        <span class="font-bold text-gray-900"><?php echo format_price($cost['total']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 text-center">
        <a href="/subscriptions/create" class="inline-block bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i> Nieuw abonnement toevoegen
        </a>
        <a href="/subscriptions" class="inline-block ml-4 bg-gray-200 text-gray-800 font-semibold py-3 px-8 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-list mr-2"></i> Alle abonnementen bekijken
        </a>
    </div>
</div>
