<?php
/**
 * Users Management List view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Gebruikersbeheer</h1>
            <p class="text-gray-600">Beheer alle gebruikers in het systeem</p>
        </div>
        <a href="/users/create" class="inline-block bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i> Nieuwe gebruiker
        </a>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($flash)): ?>
    <div class="mb-4 p-4 rounded <?php echo $flash['type'] === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
        <p><?php echo esc_html($flash['message']); ?></p>
    </div>
    <?php endif; ?>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <?php if (!empty($users)): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Naam</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">E-mail</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Rol</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Lid sinds</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Acties</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <?php echo esc_html($user['name']); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <?php echo esc_html($user['email']); ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $user['is_admin'] ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'; ?>">
                                <?php echo $user['is_admin'] ? 'Beheerder' : 'Gebruiker'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <?php echo format_date($user['created_at']); ?>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="/users/<?php echo $user['id']; ?>/edit" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i> Bewerk
                            </a>
                            <?php if ($user['id'] !== $current_user['id']): ?>
                            <form method="POST" action="/users/<?php echo $user['id']; ?>/delete" class="inline" onsubmit="return confirm('Weet u zeker dat u deze gebruiker wilt verwijderen?');">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i> Verwijder
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-8 text-center">
            <p class="text-gray-600 mb-4">Geen gebruikers gevonden</p>
            <a href="/users/create" class="inline-block bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Voeg gebruiker toe
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
