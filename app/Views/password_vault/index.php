<?php
/**
 * Password Vault List view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Wachtwoord Kluis</h1>
            <p class="text-gray-600">Alle uw wachtwoorden veilig beheerd</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="/password-vault/create" class="inline-block bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i> Wachtwoord toevoegen
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="/password-vault" class="flex gap-2">
            <input 
                type="text" 
                name="search" 
                placeholder="Zoeken naar wachtwoorden..." 
                value="<?php echo esc_attr($search); ?>"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                autocomplete="off"
            >
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Passwords Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (!empty($passwords)): ?>
            <?php foreach ($passwords as $pwd): ?>
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-800 break-words"><?php echo esc_html($pwd['title']); ?></h3>
                        <?php if (!empty($pwd['username'])): ?>
                        <p class="text-sm text-gray-600"><?php echo esc_html($pwd['username']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="flex gap-2">
                        <a href="/password-vault/<?php echo $pwd['id']; ?>" class="text-blue-600 hover:text-blue-800" title="Bekijken">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>

                <?php if (!empty($pwd['tags'])): ?>
                <div class="mb-3 flex flex-wrap gap-1">
                    <?php foreach (explode(',', $pwd['tags']) as $tag): ?>
                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                        <?php echo esc_html(trim($tag)); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($pwd['website_url'])): ?>
                <div class="mb-3 pb-3 border-b">
                    <a href="<?php echo esc_attr($pwd['website_url']); ?>" target="_blank" class="text-xs text-gray-600 hover:text-gray-900 break-all">
                        <?php echo esc_html(substr($pwd['website_url'], 0, 40)); ?> <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                <?php endif; ?>

                <div class="flex gap-2">
                    <a href="/password-vault/<?php echo $pwd['id']; ?>/edit" class="flex-1 px-3 py-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 text-center text-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="/password-vault/<?php echo $pwd['id']; ?>/delete" class="flex-1">
                        <button type="submit" class="w-full px-3 py-2 bg-red-100 text-red-600 rounded hover:bg-red-200 text-sm" onclick="return confirm('Verwijderen?');">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="col-span-full text-center py-12">
            <i class="fas fa-lock text-6xl text-gray-300 mb-4"></i>
            <p class="text-xl text-gray-600 mb-4">Geen wachtwoorden gevonden</p>
            <a href="/password-vault/create" class="inline-block bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i> Eerste wachtwoord toevoegen
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
