<?php
/**
 * Clase para manejar el sidebar automático
 */

if (!defined('ABSPATH')) {
    exit;
}

class Menu_Promesa_Sidebar {

    /**
     * Constructor
     */
    public function __construct() {
        // Solo activar si está habilitado en opciones
        if (get_option('menu_promesa_enable_sidebar', '0') === '1') {
            add_action('wp_footer', array($this, 'render_sidebar'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_sidebar_assets'));
        }
    }

    /**
     * Encolar assets del sidebar
     */
    public function enqueue_sidebar_assets() {
        // CSS del sidebar
        wp_enqueue_style(
            'menu-promesa-sidebar',
            MENU_PROMESA_PLUGIN_URL . 'assets/css/sidebar.css',
            array('menu-promesa-frontend'),
            MENU_PROMESA_VERSION
        );

        // JavaScript del sidebar
        wp_enqueue_script(
            'menu-promesa-sidebar',
            MENU_PROMESA_PLUGIN_URL . 'assets/js/sidebar.js',
            array('jquery', 'menu-promesa-frontend'),
            MENU_PROMESA_VERSION,
            true
        );

        // Pasar datos al JavaScript
        wp_localize_script('menu-promesa-sidebar', 'menuPromesaSidebarData', array(
            'baseUrl' => home_url(),
            'endpointRoute' => get_option('menu_promesa_endpoint_route', '/wp-json/custom/v1/menus/:idMenu'),
            'menuId' => get_option('menu_promesa_sidebar_menu_id', ''),
            'position' => get_option('menu_promesa_sidebar_position', 'left'),
        ));
    }

    /**
     * Renderizar el sidebar en el footer
     */
    public function render_sidebar() {
        $menu_id = get_option('menu_promesa_sidebar_menu_id', '');
        $position = get_option('menu_promesa_sidebar_position', 'left');

        if (empty($menu_id)) {
            return;
        }

        ?>
        <!-- Menu Promesa Sidebar -->
        <div id="menu-promesa-sidebar" class="menu-promesa-sidebar menu-promesa-sidebar-<?php echo esc_attr($position); ?>">
            <!-- Botón toggle para móvil -->
            <button class="menu-promesa-sidebar-toggle" aria-label="<?php esc_attr_e('Abrir/Cerrar menú', 'menu-promesa'); ?>">
                <span class="menu-promesa-hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>

            <!-- Overlay para cerrar en móvil -->
            <div class="menu-promesa-sidebar-overlay"></div>

            <!-- Contenido del sidebar -->
            <div class="menu-promesa-sidebar-content">
                <!-- Header del sidebar -->
                <div class="menu-promesa-sidebar-header">
                    <h3 class="menu-promesa-sidebar-title"><?php _e('Menú', 'menu-promesa'); ?></h3>
                    <button class="menu-promesa-sidebar-close" aria-label="<?php esc_attr_e('Cerrar menú', 'menu-promesa'); ?>">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Contenedor del menú -->
                <div class="menu-promesa-sidebar-menu" data-menu-id="<?php echo esc_attr($menu_id); ?>">
                    <div class="menu-promesa-loading"><?php _e('Cargando menú...', 'menu-promesa'); ?></div>
                    <div class="menu-promesa-content" style="display:none;"></div>
                    <div class="menu-promesa-error" style="display:none;"></div>
                </div>
            </div>
        </div>
        <?php
    }
}

// Inicializar la clase del sidebar
new Menu_Promesa_Sidebar();
