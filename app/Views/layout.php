<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html(config('app.name')); ?> - Abonnementen Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-active { @apply bg-blue-500 text-white; }
        .mobile-menu-open { @apply block; }
        .mobile-menu-hidden { @apply hidden; }
        
        /* User dropdown menu */
        .user-dropdown.active {
            @apply block;
        }
        
        /* Mobile: horizontal nav */
        @media (max-width: 767px) {
            .sidebar { 
                @apply static w-full h-auto bg-gray-800 text-white p-0;
                max-width: none;
            }
            .sidebar nav { 
                @apply flex flex-wrap gap-0;
            }
            .sidebar nav a { 
                @apply flex-1 px-2 py-2 text-sm text-center;
            }
        }
        
        /* Desktop: vertical sidebar */
        @media (min-width: 768px) {
            .sidebar { 
                @apply fixed left-0 top-16 h-full w-64;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/dashboard" class="flex items-center">
                        <i class="fas fa-wallet text-blue-600 text-2xl mr-2"></i>
                        <span class="font-bold text-xl text-gray-800">Mijn Abonnementen</span>
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    <?php if (is_logged_in()): ?>
                        <span class="text-gray-700 hidden sm:inline"><?php echo esc_html(auth_user()['name']); ?></span>
                        <div class="relative">
                            <button id="user-menu-toggle" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-user-circle text-2xl"></i>
                            </button>
                            <div id="user-menu-dropdown" class="user-dropdown hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                <a href="/change-password" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-key mr-2"></i> Wachtwoord wijzigen
                                </a>
                                <?php if (auth_user()['is_admin']): ?>
                                <a href="/users" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-users mr-2"></i> Gebruikersbeheer
                                </a>
                                <?php endif; ?>
                                <a href="/logout" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-md">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Uitloggen
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Container met sidebar + content -->
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar / Mobile Nav -->
        <?php if (is_logged_in()): ?>
        <aside id="sidebar" class="sidebar bg-gray-800 text-white w-full md:w-64 md:min-h-screen md:fixed md:left-0 md:top-16 md:z-40">
            <div class="p-0 md:p-4">
                <nav class="flex flex-wrap md:flex-col md:space-y-2">
                    <a href="/dashboard" class="flex-1 md:flex-none block px-3 md:px-4 py-2 rounded-lg hover:bg-gray-700 text-sm md:text-base text-center md:text-left <?php echo strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'sidebar-active' : ''; ?>">
                        <i class="fas fa-home mr-0 md:mr-3 w-5 md:w-auto"></i> <span class="hidden md:inline">Dashboard</span>
                    </a>
                    
                    <a href="/subscriptions" class="flex-1 md:flex-none block px-3 md:px-4 py-2 rounded-lg hover:bg-gray-700 text-sm md:text-base text-center md:text-left <?php echo strpos($_SERVER['REQUEST_URI'], '/subscriptions') !== false && strpos($_SERVER['REQUEST_URI'], '/password') === false ? 'sidebar-active' : ''; ?>">
                        <i class="fas fa-layer-group mr-0 md:mr-3 w-5 md:w-auto"></i> <span class="hidden md:inline">Abonnementen</span>
                    </a>
                    
                    <a href="/costs" class="flex-1 md:flex-none block px-3 md:px-4 py-2 rounded-lg hover:bg-gray-700 text-sm md:text-base text-center md:text-left <?php echo strpos($_SERVER['REQUEST_URI'], '/costs') !== false ? 'sidebar-active' : ''; ?>">
                        <i class="fas fa-chart-line mr-0 md:mr-3 w-5 md:w-auto"></i> <span class="hidden md:inline">Kosten</span>
                    </a>
                    
                    <a href="/password-vault" class="flex-1 md:flex-none block px-3 md:px-4 py-2 rounded-lg hover:bg-gray-700 text-sm md:text-base text-center md:text-left <?php echo strpos($_SERVER['REQUEST_URI'], '/password-vault') !== false ? 'sidebar-active' : ''; ?>">
                        <i class="fas fa-key mr-0 md:mr-3 w-5 md:w-auto"></i> <span class="hidden md:inline">Wachtwoord Kluis</span>
                    </a>
                    
                    <a href="/categories" class="flex-1 md:flex-none block px-3 md:px-4 py-2 rounded-lg hover:bg-gray-700 text-sm md:text-base text-center md:text-left <?php echo strpos($_SERVER['REQUEST_URI'], '/categories') !== false ? 'sidebar-active' : ''; ?>">
                        <i class="fas fa-tags mr-0 md:mr-3 w-5 md:w-auto"></i> <span class="hidden md:inline">Categorieën</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Overlay voor mobile sidebar -->
        <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden"></div>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="flex-1 w-full md:ml-64">
            <!-- Flash messages -->
            <?php if (!empty($flash)): ?>
            <div class="px-4 py-2">
                <div class="max-w-7xl mx-auto">
                    <?php 
                    $bg_color = $flash['type'] === 'success' ? 'bg-green-100' : 'bg-red-100';
                    $text_color = $flash['type'] === 'success' ? 'text-green-800' : 'text-red-800';
                    $border_color = $flash['type'] === 'success' ? 'border-green-400' : 'border-red-400';
                    ?>
                    <div class="<?php echo $bg_color; ?> border-l-4 <?php echo $border_color; ?> <?php echo $text_color; ?> p-4 mt-4 rounded">
                        <p class="font-bold"><?php echo $flash['type'] === 'success' ? 'Succes' : 'Fout'; ?></p>
                        <p><?php echo esc_html($flash['message']); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php echo $content; ?>
        </main>
    </div>

    <script>
        // User menu dropdown toggle
        const userMenuToggle = document.getElementById('user-menu-toggle');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');

        if (userMenuToggle) {
            userMenuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
                userMenuDropdown.classList.toggle('active');
            });

            // Close menu when clicking a link
            userMenuDropdown.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    userMenuDropdown.classList.add('hidden');
                    userMenuDropdown.classList.remove('active');
                });
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.relative')) {
                    userMenuDropdown.classList.add('hidden');
                    userMenuDropdown.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>
