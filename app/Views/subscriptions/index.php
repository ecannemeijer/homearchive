<?php
/**
 * Subscriptions List view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Abonnementen & Verzekeringen</h1>
            <p class="text-gray-600">Overzicht van al uw items</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="/subscriptions/create" class="inline-block bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i> Nieuw item
            </a>
        </div>
    </div>

    <!-- Search & Filter Bars -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="/subscriptions" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Zoeken..." 
                        value="<?php echo esc_attr($filters['search']); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Type Filter -->
                <div>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Alle types</option>
                        <option value="subscription" <?php echo $filters['type'] === 'subscription' ? 'selected' : ''; ?>>Abonnement</option>
                        <option value="insurance" <?php echo $filters['type'] === 'insurance' ? 'selected' : ''; ?>>Verzekering</option>
                    </select>
                </div>

                <!-- Frequency Filter -->
                <div>
                    <select name="frequency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Alle frequenties</option>
                        <option value="monthly" <?php echo $filters['frequency'] === 'monthly' ? 'selected' : ''; ?>>Maandelijks</option>
                        <option value="yearly" <?php echo $filters['frequency'] === 'yearly' ? 'selected' : ''; ?>>Jaarlijks</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="name" <?php echo $filters['sort_by'] === 'name' ? 'selected' : ''; ?>>Naam (A-Z)</option>
                        <option value="cost" <?php echo $filters['sort_by'] === 'cost' ? 'selected' : ''; ?>>Laagste prijs</option>
                        <option value="end_date" <?php echo $filters['sort_by'] === 'end_date' ? 'selected' : ''; ?>>Einddatum</option>
                        <option value="created_at" <?php echo $filters['sort_by'] === 'created_at' ? 'selected' : ''; ?>>Meest recent</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <select name="limit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="10" <?php echo $filters['limit'] === 10 ? 'selected' : ''; ?>>10 per pagina</option>
                        <option value="25" <?php echo $filters['limit'] === 25 ? 'selected' : ''; ?>>25 per pagina</option>
                        <option value="50" <?php echo $filters['limit'] === 50 ? 'selected' : ''; ?>>50 per pagina</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i> Toepassen
                </button>
                <a href="/subscriptions" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <?php if (!empty($subscriptions)): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Naam</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Frequentie</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Bedrag</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Einddatum</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Acties</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($subscriptions as $sub): 
                        $is_expiring = is_expiring_soon($sub['end_date']);
                    ?>
                    <tr class="hover:bg-gray-50 <?php echo $is_expiring ? 'bg-yellow-50' : ''; ?>">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <?php echo esc_html($sub['name']); ?>
                            <?php if ($is_expiring): ?>
                                <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Verloping
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $sub['type'] === 'subscription' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                <?php echo type_label($sub['type']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo frequency_label($sub['frequency']); ?></td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?php echo format_price($sub['cost']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <?php if ($sub['is_monthly_cancelable']): ?>
                                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">
                                    <i class="fas fa-calendar-times mr-1"></i> Maandelijks opzegbaar
                                </span>
                            <?php else: ?>
                                <?php echo format_date($sub['end_date']); ?>
                                <?php if (!empty($sub['end_date'])): 
                                    $days = days_until($sub['end_date']);
                                    if ($days !== null):
                                ?>
                                    <span class="text-xs text-gray-500">(<?php echo $days >= 0 ? $days . ' dag' . ($days !== 1 ? 'en' : '') : 'verlopen'; ?>)</span>
                                <?php endif; endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $sub['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo $sub['is_active'] ? 'Actief' :'Inactief'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="/subscriptions/<?php echo $sub['id']; ?>" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/subscriptions/<?php echo $sub['id']; ?>/edit" class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="/subscriptions/<?php echo $sub['id']; ?>/delete" class="inline" onsubmit="return confirm('Weet u zeker?');">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <div class="bg-gray-50 border-t px-6 py-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Pagina <strong><?php echo $page; ?></strong> van <strong><?php echo $pages; ?></strong> 
                (<strong><?php echo $total; ?></strong> items)
            </div>
            <div class="space-x-2">
                <?php if ($page > 1): ?>
                <a href="?page=1&limit=<?php echo $filters['limit']; ?><?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                    <i class="fas fa-chevron-left"></i> Eerste
                </a>
                <?php endif; ?>

                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&limit=<?php echo $filters['limit']; ?><?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                    Vorige
                </a>
                <?php endif; ?>

                <span class="px-3 py-1">... <?php echo $page; ?> ...</span>

                <?php if ($page < $pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&limit=<?php echo $filters['limit']; ?><?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                    Volgende
                </a>
                <?php endif; ?>

                <?php if ($page < $pages): ?>
                <a href="?page=<?php echo $pages; ?>&limit=<?php echo $filters['limit']; ?><?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                    Laatste <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="p-8 text-center">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-xl text-gray-600 mb-4">Geen abonnementen gevonden</p>
            <a href="/subscriptions/create" class="inline-block bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i> Eerste abonnement toevoegen
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
