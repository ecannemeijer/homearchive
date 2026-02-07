<?php
/**
 * Categories Management view
 */
?>
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Categorieën beheren</h1>
        <p class="text-gray-600">Organiseer uw abonnementen met tags</p>
    </div>

    <!-- Add New Category Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Nieuwe categorie toevoegen</h2>
        <form id="add-category-form" class="flex gap-2">
            <input type="text" name="name" placeholder="Categorie naam" required class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <input type="color" name="color" value="#3B82F6" class="h-10 rounded-lg cursor-pointer">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Toevoegen
            </button>
        </form>
    </div>

    <!-- Categories List -->
    <div class="space-y-3">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
            <div class="bg-white rounded-lg shadow p-4 flex items-center justify-between group">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-8 h-8 rounded" style="background-color: <?php echo esc_attr($cat['color']); ?>;"></div>
                    <div>
                        <p class="font-semibold text-gray-900"><?php echo esc_html($cat['name']); ?></p>
                        <p class="text-xs text-gray-600"><?php echo esc_attr($cat['color']); ?></p>
                    </div>
                </div>
                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                    <button class="edit-cat px-3 py-2 bg-yellow-100 text-yellow-600 rounded hover:bg-yellow-200" data-id="<?php echo $cat['id']; ?>" data-name="<?php echo esc_attr($cat['name']); ?>" data-color="<?php echo esc_attr($cat['color']); ?>">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form method="POST" class="delete-form" data-id="<?php echo $cat['id']; ?>">
                        <button type="submit" class="px-3 py-2 bg-red-100 text-red-600 rounded hover:bg-red-200" onclick="return confirm('Verwijderen?');">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <i class="fas fa-tags text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-600">Geen categorieën aangemaakt</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Add category
    document.getElementById('add-category-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/api/categories', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Fout: ' + (data.error || 'Toevoegen mislukt'));
            }
        });
    });

    // Delete category
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = this.dataset.id;
            const formData = new FormData();
            
            fetch('/api/categories/' + id + '/delete', {
                method: 'POST',
                body: formData
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Fout voor verwijderen');
                }
            });
        });
    });

    // Edit category
    document.querySelectorAll('.edit-cat').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const color = this.dataset.color;
            
            const newName = prompt('Nieuwe naam:', name);
            if (!newName) return;

            const formData = new FormData();
            formData.append('name', newName);
            formData.append('color', color);

            fetch('/api/categories/' + id, {
                method: 'POST',
                body: formData
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Fout bij bijwerken');
                }
            });
        });
    });
</script>
