<?php
/**
 * Show Subscription view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2"><?php echo esc_html($subscription['name']); ?></h1>
            <p class="text-gray-600">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo $subscription['type'] === 'subscription' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?> mr-2">
                    <?php echo type_label($subscription['type']); ?>
                </span>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo $subscription['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                    <?php echo $subscription['is_active'] ? 'Actief' : 'Inactief'; ?>
                </span>
            </p>
        </div>
        <div class="space-y-2">
            <a href="/subscriptions/<?php echo $subscription['id']; ?>/edit" class="block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center">
                <i class="fas fa-edit mr-2"></i> Bewerken
            </a>
            <form method="POST" action="/subscriptions/<?php echo $subscription['id']; ?>/delete" class="inline" onsubmit="return confirm('Weet u zeker?');">
                <button type="submit" class="w-full px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i> Verwijderen
                </button>
            </form>
        </div>
    </div>

    <!-- Main content grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Details Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Financieel -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-coins text-blue-600 mr-2"></i> Financieel
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 text-sm">Bedrag</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo format_price($subscription['cost']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Frequentie</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo frequency_label($subscription['frequency']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Maandelijks</p>
                        <p class="text-lg font-semibold text-gray-900">
                            <?php 
                            $monthly = $subscription['frequency'] === 'yearly' ? $subscription['cost'] / 12 : $subscription['cost'];
                            echo format_price($monthly);
                            ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Jaarlijks</p>
                        <p class="text-lg font-semibold text-gray-900">
                            <?php 
                            $yearly = $subscription['frequency'] === 'yearly' ? $subscription['cost'] : $subscription['cost'] * 12;
                            echo format_price($yearly);
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Datums -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-calendar text-purple-600 mr-2"></i> Datums
                </h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-600 text-sm">Startdatum</p>
                        <p class="text-lg text-gray-900"><?php echo format_date($subscription['start_date']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Einddatum</p>
                        <?php if ($subscription['is_monthly_cancelable']): ?>
                            <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-calendar-times mr-1"></i> Maandelijks opzegbaar
                            </span>
                        <?php else: ?>
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo format_date($subscription['end_date']); ?>
                                <?php if (!empty($subscription['end_date'])): 
                                    $days = days_until($subscription['end_date']);
                                    if ($days !== null):
                                ?>
                                    <span class="ml-2 text-sm <?php echo is_expiring_soon($subscription['end_date']) ? 'text-yellow-600' : 'text-gray-600'; ?>">
                                        <?php echo $days >= 0 ? $days . ' dag' . ($days !== 1 ? 'en' : '') . ' tot verloop' : 'Verlopen'; ?>
                                    </span>
                                <?php endif; endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Factureringsdag</p>
                        <p class="text-lg text-gray-900"><?php echo $subscription['billing_date'] ? 'Dag ' . $subscription['billing_date'] : '-'; ?></p>
                    </div>
                </div>
            </div>

            <!-- Inloggegevens (met wachtwoord) -->
            <?php if (!empty($subscription['username']) || !empty($subscription['password'] ?? null) || !empty($subscription['website_url'])): ?>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-lock text-red-600 mr-2"></i> Inloggegevens
                </h2>
                
                <?php if (!empty($subscription['website_url'])): ?>
                <div class="mb-3">
                    <p class="text-gray-600 text-sm">Website</p>
                    <a href="<?php echo esc_attr($subscription['website_url']); ?>" target="_blank" class="text-blue-600 hover:underline break-all">
                        <?php echo esc_html($subscription['website_url']); ?> <i class="fas fa-external-link-alt text-xs"></i>
                    </a>
                </div>
                <?php endif; ?>

                <?php if (!empty($subscription['username'])): ?>
                <div class="mb-3">
                    <p class="text-gray-600 text-sm">Gebruikersnaam/Email</p>
                    <div class="flex items-center justify-between bg-gray-100 p-2 rounded">
                        <input type="text" readonly value="<?php echo esc_attr($subscription['username']); ?>" class="bg-transparent flex-1 text-gray-900">
                        <button class="px-3 py-1 text-blue-600 hover:text-blue-800 copy-btn" data-value="<?php echo esc_attr($subscription['username']); ?>">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($subscription['password'] ?? null)): ?>
                <div>
                    <p class="text-gray-600 text-sm">Wachtwoord</p>
                    <div class="flex items-center gap-2">
                        <input type="password" id="pwd-<?php echo $subscription['id']; ?>" readonly value="<?php echo esc_attr($subscription['password']); ?>" class="bg-gray-100 flex-1 px-3 py-2 rounded">
                        <button type="button" class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 toggle-pwd" data-target="pwd-<?php echo $subscription['id']; ?>">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 copy-btn" data-value="<?php echo esc_attr($subscription['password']); ?>">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Opmerkingen -->
            <?php if (!empty($subscription['notes'])): ?>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-sticky-note text-yellow-600 mr-2"></i> Opmerkingen
                </h2>
                <p class="text-gray-700 whitespace-pre-wrap"><?php echo esc_html($subscription['notes']); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Categorie -->
            <?php if (!empty($subscription['category'])): ?>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-800 mb-3">Categorie</h3>
                <p class="px-3 py-1 inline-block bg-blue-100 text-blue-800 rounded-full text-sm">
                    <?php echo esc_html($subscription['category']); ?>
                </p>
            </div>
            <?php endif; ?>

            <!-- Documents -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-file-upload mr-2 text-green-600"></i> Documenten
                </h3>

                <?php if (!empty($documents)): ?>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    <?php foreach ($documents as $doc): ?>
                    <div class="bg-gray-50 p-3 rounded flex items-center justify-between group">
                        <div class="flex items-center flex-1">
                            <i class="fas fa-file text-gray-600 mr-2"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 truncate"><?php echo esc_html($doc['original_filename']); ?></p>
                                <p class="text-xs text-gray-600"><?php echo round($doc['file_size'] / 1024, 2); ?> KB</p>
                            </div>
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 flex gap-1">
                            <a href="/documents/<?php echo $doc['id']; ?>/download" class="px-2 py-1 text-blue-600 hover:text-blue-800" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-600 text-sm">Geen documenten</p>
                <?php endif; ?>

                <label class="mt-4 block">
                    <span class="cursor-pointer px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 inline-block text-sm">
                        <i class="fas fa-upload mr-2"></i> Document toevoegen
                    </span>
                    <input type="file" id="doc-upload-<?php echo $subscription['id']; ?>" class="hidden" data-sub-id="<?php echo $subscription['id']; ?>">
                </label>
            </div>

            <!-- Metadata -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">Metadata</h3>
                <div class="space-y-2 text-sm">
                    <p class="text-gray-600"><strong>Toegevoegd:</strong> <?php echo format_date($subscription['created_at'], 'j M Y H:i'); ?></p>
                    <p class="text-gray-600"><strong>Bijgewerkt:</strong> <?php echo format_date($subscription['updated_at'], 'j M Y H:i'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-pwd').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
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
    });

    // Copy to clipboard
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.dataset.value;
            navigator.clipboard.writeText(text).then(() => {
                const originalIcon = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => {
                    this.innerHTML = originalIcon;
                }, 2000);
            });
        });
    });

    // File upload
    document.getElementById('doc-upload-<?php echo $subscription['id']; ?>')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('subscription_id', this.dataset.subId);

        fetch('/api/documents/upload', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Fout: ' + (data.error || 'Upload mislukt'));
            }
        });
    });
</script>
