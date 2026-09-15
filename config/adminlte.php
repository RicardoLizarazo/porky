<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Piqueteadero Porky de la 105',
    'title_prefix' => 'PP105',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>PP</b>105',
    'logo_img' => 'vendor/adminlte/dist/img/logo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Piqueteadero Porky de la 105',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logo.png',
            'alt' => 'Piqueteadero Porky de la 105',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => true,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => 'font-weight-bold',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4 bg-danger',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-dark bg-danger',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout-any',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => false,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],
        [
            'text' => 'Menu',
            'url'  => 'menu',
            'icon' => 'fas fa-utensils',
        ],
        [
            'text'=>'Mesas',
            'url' =>'floor-map',
            'icon' => 'fas fa-border-all',
            'can' => 'floor_map.view',
        ],
        [
            'text' => 'Caja',
            'icon' => 'fas fa-cash-register',
            'can'  => 'cashier.open',

            'submenu' => [

                [
                    'text'   => 'Apertura de Caja',
                    'url'    => 'cash/open',
                    'icon'   => 'fas fa-lock-open',
                    'can'    => 'cashier.open',
                    'active' => ['cash/open*'],
                ],

                [
                    'text'   => 'Dashboard',
                    'url'    => 'cashier',
                    'icon'   => 'fas fa-desktop',
                    'can'    => 'cashier.open',
                    'active' => ['cashier'],
                ],

                [
                    'text'   => 'Cobro de Mesas',
                    'url'    => 'cashier/orders',
                    'icon'   => 'fas fa-receipt',
                    'can'    => 'cashier.charge',
                    'active' => ['cashier/orders*'],
                ],
                [
                    'text'   => 'Cerrar Caja',
                    'url'    => 'cash/close',
                    'icon'   => 'fas fa-lock',
                    'can'    => 'cashier.open',
                    'active' => ['cash/close*'],
                ],
            ],
        ],
        [
            'text' => 'Mis pedidos',
            'url'  => 'mis-pedidos',
            'icon' => 'fas fa-receipt',
            'can'  => 'customer.only',
        ],
        [
            'text' => 'Dashboard',
            'url'  => 'dashboard',
            'icon' => 'fas fa-chart-bar',
            'can' => 'dashboard.view',
        ],
        [
            'text' => 'Restaurante',
            'url'  => 'admin/settings',
            'icon' => 'fas fa-store',
            'submenu'=>[
                [
                    'text'=>'Ubicaciones',
                    'url' =>'locations',
                    'icon' => 'fas fa-map-marker-alt',
                    'can' => 'locations.view',
                    'active' => ['restaurante/locations*'], 
                ],
                [
                    'text'=>'Pisos',
                    'url' =>'floors',
                    'icon' => 'fas fa-layer-group',
                    'can' => 'floors.view',
                    'active' => ['restaurante/floors*'], 
                ],
                [
                    'text'=>'Mesas',
                    'url' =>'tables',
                    'icon' => 'fas fa-chair',
                    'can' => 'tables.view',
                    'active' => ['restaurante/tables*'],
                ],
                [
                    'text'   => 'Cajas',
                    'url'    => 'cash-registers',
                    'icon'   => 'fas fa-cash-register',
                    'can'    => 'cash-registers.view',
                    'active' => ['restaurante/cash-registers*'],
                ],
                [
                    'text'=>'Categorias',
                    'url' =>'categories',
                    'icon' => 'fas fa-tags',
                    'can' => 'categories.view',
                    'active' => ['restaurante/categories*'], 
                ],
                [
                    'text'=>'Productos',
                    'url' =>'products',
                    'icon' => 'fas fa-shopping-basket',
                    'can' => 'products.view',
                    'active' => ['restaurante/products*'], 
                ],
            ],
        ],
        [
            'text' => 'Cocina',
            'url'  => '#',
            'icon' => 'fas fa-fire',
            'submenu' => [
                [
                    'text'   => 'Parrilla',
                    'url'    => 'kitchen-board/1',
                    'icon'   => 'fas fa-fire text-danger',
                    'can'    => 'kitchen.view',
                    'active' => ['kitchen-board/1'],
                ],
                [
                    'text'   => 'Sopas',
                    'url'    => 'kitchen-board/2',
                    'icon'   => 'fas fa-fire text-info',
                    'can'    => 'kitchen.view',
                    'active' => ['kitchen-board/2'],
                ],

                [
                    'text'   => 'Picadas',
                    'url'    => 'kitchen-board/3',
                    'icon'   => 'fas fa-fire text-warning',
                    'can'    => 'kitchen.view',
                    'active' => ['kitchen-board/3'],
                ],
                [
                    'text'   => 'Bebidas',
                    'url'    => 'kitchen-board/4',
                    'icon'   => 'fas fa-fire text-success',
                    'can'    => 'kitchen.view',
                    'active' => ['kitchen-board/4'],
                ],
                [
                    'text'   => 'Jugos',
                    'url'    => 'kitchen-board/5',
                    'icon'   => 'fas fa-fire text-info',
                    'can'    => 'kitchen.view',
                    'active' => ['kitchen-board/5'],
                ],
                [
                    'text'   => 'Parrilla + Sopas (TV)',
                    'url'    => 'kitchen-board/1,2',
                    'icon'   => 'fas fa-fire text-danger',
                    'can'    => 'kitchen.view',
                    'active' => ['kitchen-board/1,2'],
                ],
                [
                    'text'   => 'Parrilla + Picadas (TV)',
                    'url'    => 'kitchen-board/1,2',
                    'icon'   => 'fas fa-fire text-danger',
                    'can'    => 'kitchen.view',
                    'active' => ['kitchen-board/1,2'],
                ],

                [
                    'text'   => 'Despacho Parrilla',
                    'url'    => 'kitchen-dispatch/1',
                    'icon'   => 'fas fa-check-circle text-danger',
                    'can' => 'kitchen.dispatch',
                    'active' => ['kitchen-dispatch/1'],
                ],
                [
                    'text'   => 'Despacho Sopas',
                    'url'    => 'kitchen-dispatch/2',
                    'icon'   => 'fas fa-check-circle text-info',
                    'can' => 'kitchen.dispatch',
                    'active' => ['kitchen-dispatch/2'],
                ],
                [
                    'text'   => 'Despacho Picadas',
                    'url'    => 'kitchen-dispatch/3',
                    'icon'   => 'fas fa-check-circle text-warning',
                    'can' => 'kitchen.dispatch',
                    'active' => ['kitchen-dispatch/3'],
                ],
                [
                    'text'   => 'Despacho Bebidas',
                    'url'    => 'kitchen-dispatch/4',
                    'icon'   => 'fas fa-check-circle text-success',
                    'can' => 'kitchen.dispatch',
                    'active' => ['kitchen-dispatch/4'],
                ],
                [
                    'text'   => 'Despacho Jugos',
                    'url'    => 'kitchen-dispatch/5',
                    'icon'   => 'fas fa-check-circle text-info',
                    'can'    => 'kitchen.dispatch',
                    'active' => ['kitchen-dispatch/5'],
                ],
                [
                    'text'   => 'Despacho Parrilla + Sopas',
                    'url'    => 'kitchen-dispatch/1,2',
                    'icon'   => 'fas fa-check-circle text-danger',
                    'can'    => 'kitchen.dispatch',
                    'active' => ['kitchen-dispatch/1,2'],
                ],
                [
                    'text'   => 'Despacho Parrilla + Picadas',
                    'url'    => 'kitchen-dispatch/1,3',
                    'icon'   => 'fas fa-check-circle text-danger',
                    'can'    => 'kitchen.dispatch',
                    'active' => ['kitchen-dispatch/1,2'],
                ],

            ],
        ],
        [
            'text' => 'Pedidos',
            'url'  => 'admin/settings',
            'icon' => 'fas fa-store',
            'submenu'=>[
                [
                    'text'=>'Pedidos',
                    'url' =>'orders',
                    'icon' => 'far fa-file-alt',
                    'can' => 'orders.view',
                    'active' => ['orders*'], 
                ],
                [
                    'text'=>'Clientes',
                    'url' =>'customers',
                    'icon' => 'fas fa-users',
                    'can' => 'customers.view',
                    'active' => ['customers*'],
                ],
            ],
        ],
        [
            'text' => 'Inventario',
            'url'  => 'inventory-items',
            'icon' => 'fas fa-warehouse',
            'submenu' => [
                [
                    'text' => 'Unidades de Medida',
                    'url'  => 'units',
                    'icon' => 'fas fa-ruler',
                    'can'  => 'units.view',
                    'active' => ['units*'],
                ],
                [
                    'text' => 'Productos de Inventario',
                    'url'  => 'inventory-items',
                    'icon' => 'fas fa-boxes',
                    'can'  => 'inventory_items.view',
                    'active' => ['inventory-items*'],
                ],
                [
                    'text' => 'Proveedores',
                    'url'  => 'suppliers',
                    'icon' => 'fas fa-truck',
                    'can'  => 'suppliers.view',
                    'active' => ['suppliers*'],
                ],
                [
                    'text' => 'Compras',
                    'url'  => 'purchases',
                    'icon' => 'fas fa-file-invoice',
                    'can'  => 'purchases.view',
                    'active' => ['purchases*'],
                ],
                [
                    'text' => 'Devoluciones a Proveedor',
                    'url'  => 'purchase-returns',
                    'icon' => 'fas fa-undo',
                    'can'  => 'purchase_returns.view',
                    'active' => ['purchase-returns*'],
                ],
                [
                    'text' => 'Salidas y Ajustes',
                    'url'  => 'inventory-movements',
                    'icon' => 'fas fa-dolly',
                    'can'  => 'inventory_movements.view',
                    'active' => ['inventory-movements*'],
                ],
                [
                    'text' => 'Historial de Precios',
                    'url'  => 'price-history',
                    'icon' => 'fas fa-chart-line',
                    'can'  => 'price_history.view',
                    'active' => ['price-history*'],
                ],
                [
                    'text' => 'Pendientes de Inventario',
                    'url'  => 'inventory-sync-issues',
                    'icon' => 'fas fa-exclamation-triangle',
                    'can'  => 'inventory_sync_issues.view',
                    'active' => ['inventory-sync-issues*'],
                ],
            ],
        ],
        [
            'text' => 'Rutas',
            'url'  => 'routes',
            'icon' => 'fas fa-route',
            'can'  => 'routes.view',
            'active' => ['routes*'],
        ],
        [
            'text' => 'Reportes',
            'url'  => 'reports',
            'icon' => 'fas fa-chart-line',
            'can'  => 'reports.view',
            'submenu' => [
                [
                    'text' => 'Productos vendidos',
                    'url'  => 'reports/products',
                    'icon' => 'fas fa-box',
                    'can' => 'reports.products',
                    'active' => ['reports/products*'],
                ],
                [
                    'text' => 'Clientes frecuentes',
                    'url'  => 'reports/customers',
                    'icon' => 'fas fa-user-check',
                    'can' => 'reports.customers',
                    'active' => ['reports/customers*'],
                ],
                [
                    'text' => 'Domiciliarios',
                    'url'  => 'reports/delivery',
                    'icon' => 'fas fa-motorcycle',
                    'can' => 'reports.delivery',
                    'active' => ['reports/delivery'],
                ],
                [
                    'text' => 'Domiciliario detallado',
                    'url'  => 'reports/delivery_detail',
                    'icon' => 'fas fa-clipboard-list',
                    'can' => 'reports.delivery',
                    'active' => ['reports/delivery_detail*'],
                ],
                [
                    'text' => 'Métodos de pago',
                    'url'  => 'reports/payments',
                    'icon' => 'fas fa-credit-card',
                    'can' => 'reports.payments',
                    'active' => ['reports/payments*'],
                ],
            ],
        ],
        [
            'text' => 'Reportes Restaurante',
            'url'  => 'restaurant-reports',
            'icon' => 'fas fa-utensils',
            'can'  => 'restaurant_reports.view',
            'submenu' => [
                [
                    'text' => 'Dashboard Comercial',
                    'url'  => 'restaurant-reports',
                    'icon' => 'fas fa-chart-pie',
                    'can'  => 'restaurant_reports.dashboard',
                    'active' => ['restaurant-reports'],
                ],
                [
                    'text' => 'Ventas por Piso',
                    'url'  => 'restaurant-reports/floor',
                    'icon' => 'fas fa-building',
                    'can'  => 'restaurant_reports.floor',
                    'active' => ['restaurant-reports/floor*'],
                ],
                [
                    'text' => 'Ventas por Mesa',
                    'url'  => 'restaurant-reports/table',
                    'icon' => 'fas fa-chair',
                    'can'  => 'restaurant_reports.table',
                    'active' => ['restaurant-reports/table*'],
                ],
                [
                    'text' => 'Ventas por Mesero',
                    'url'  => 'restaurant-reports/waiter',
                    'icon' => 'fas fa-user-tie',
                    'can'  => 'restaurant_reports.waiter',
                    'active' => ['restaurant-reports/waiter*'],
                ],
                [
                    'text' => 'Historial Despachos',
                    'url'  => 'kitchen-dispatch-history',
                    'icon' => 'fas fa-history nav-icon',
                    'can'  => 'kitchen_dispatch.view_all',
                    'active' => ['kitchen_dispatch/view_all*'],
                ],
            ],
        ],
        [
            'text' => 'Seguridad',
            'url' => 'admin/security',
            'icon' => 'fas fa-shield-alt',
            'submenu' => [
                [
                    'text' => 'Usuarios',
                    'url' => 'users',
                    'icon' => 'fas fa-user-friends',
                    'can' => 'users.view',
                    'active' => ['security/users*'], 
                ],
                [
                    'text' => 'Roles',
                    'url' => 'roles',
                    'icon' => 'fas fa-user-tag',
                    'can' => 'roles.view',
                    'active' => ['security/roles*'], 

                ],
                [
                    'text' => 'Permisos',
                    'url' => 'permissions',
                    'icon' => 'fas fa-lock',
                    'can' => 'permissions.view',
                    'active' => ['security/permissions*'], 
                ],
                [
                    'text' => 'Auditoria',
                    'url' => 'audit-logs',
                    'icon' => 'fas fa-history nav-icon',
                    'can' => 'audit_logs.view',
                    'active' => ['security/audit-logs*'], 
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
