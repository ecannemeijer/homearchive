<?php
/**
 * Show Password view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo esc_html($password['title']); ?></h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        <?php if (!empty($password['website_url'])): ?>
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Website</label>
            <a href="<?php echo esc_attr($password['website_url']); ?>" target="_blank" class="text-blue-600 hover:underline break-all">
                <?php echo esc_html($password['website_url']); ?> <i class="fas fa-external-link-alt text-xs"></i>
            </a>
        </div>
        <?php endif; ?>

        <?php if (!empty($password['username'])): ?>
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Gebruikersnaam/Email</label>
            <div class="flex items-center gap-2 bg-gray-100 p-3 rounded">
                <input type="text" readonly value="<?php echo esc_attr($password['username']); ?>" class="bg-transparent flex-1">
                <button class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 copy-btn" data-value="<?php echo esc_attr($password['username']); ?>">
                    <i class="fas fa-copy"></i> Kopiëren
                </button>
            </div>
        </div>
        <?php endif; ?>

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Wachtwoord</label>
            <div class="flex items-center gap-2 bg-gray-100 p-3 rounded">
                <input type="password" id="pwd-display" readonly value="<?php echo esc_attr($password['password_decrypted']); ?>" class="bg-transparent flex-1">
                <button type="button" id="toggle-pwd" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                    <i class="fas fa-eye"></i>
                </button>
                <button type="button" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 copy-btn" data-value="<?php echo esc_attr($password['password_decrypted']); ?>">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>

        <?php if (!empty($password['tags'])): ?>
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Tags</label>
            <div class="flex flex-wrap gap-2">
                <?php foreach (explode(',', $password['tags']) as $tag): ?>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    <?php echo esc_html(trim($tag)); ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($password['notes'])): ?>
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Notities</label>
            <p class="text-gray-700 bg-gray-50 p-3 rounded whitespace-pre-wrap"><?php echo esc_html($password['notes']); ?></p>
        </div>
        <?php endif; ?>

        <div class="flex gap-4 pt-4 border-t">
            <a href="/password-vault/<?php echo $password['id']; ?>/edit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center">
                <i class="fas fa-edit mr-2"></i> Bewerken
            </a>
            <a href="/password-vault" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 text-center">
                <i class="fas fa-arrow-left mr-2"></i> Terug
            </a>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggle-pwd').addEventListener('click', function() {
        const input = document.getElementById('pwd-display');
        if (input.type === 'password') {
            input.type = 'text';
            this.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            input.type = 'password';
            this.innerHTML = '<i class="fas fa-eye"></i>';
        }
    });

    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            navigator.clipboard.writeText(this.dataset.value).then(() => {
                const original = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Gekopieerd!';
                setTimeout(() => {
                    this.innerHTML = original;
                }, 2000);
            });
        });
    });
</script>
