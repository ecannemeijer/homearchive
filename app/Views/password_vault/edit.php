<?php
/**
 * Edit Password view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Wachtwoord bewerken</h1>
        <p class="text-gray-600"><?php echo esc_html($password['title']); ?></p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="/password-vault/<?php echo $password['id']; ?>" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
                <input type="text" id="title" name="title" value="<?php echo esc_attr($password['title']); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Gebruikersnaam/Email</label>
                    <input type="text" id="username" name="username" value="<?php echo esc_attr($password['username']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="website_url" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" id="website_url" name="website_url" value="<?php echo esc_attr($password['website_url']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Wachtwoord (laat leeg voor ongewijzigd)</label>
                <div class="flex gap-2">
                    <input type="password" id="password" name="password" value="<?php echo esc_attr($password['password_decrypted']); ?>" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <button type="button" id="toggle-password" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                <input type="text" id="tags" name="tags" value="<?php echo esc_attr($password['tags']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="kommaseparated">
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
                <textarea id="notes" name="notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo esc_html($password['notes']); ?></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i> Bijwerken
                </button>
                <a href="/password-vault/<?php echo $password['id']; ?>" class="flex-1 bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg hover:bg-gray-400 transition text-center">
                    <i class="fas fa-times mr-2"></i> Annuleren
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('toggle-password').addEventListener('click', function() {
        const input = document.getElementById('password');
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
