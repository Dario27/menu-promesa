<?php
/**
 * Clase para la administración del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Menu_Promesa_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Agregar menú de administración
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Menu Promesa', 'menu-promesa'),
            __('Menu Promesa', 'menu-promesa'),
            'manage_options',
            'menu-promesa',
            array($this, 'render_admin_page'),
            'dashicons-menu-alt',
            30
        );
    }

    /**
     * Registrar configuraciones
     */
    public function register_settings() {
        register_setting('menu_promesa_settings', 'menu_promesa_endpoint_route');
        register_setting('menu_promesa_settings', 'menu_promesa_list_endpoint');

        add_settings_section(
            'menu_promesa_api_section',
            __('Configuración de API', 'menu-promesa'),
            array($this, 'render_section_info'),
            'menu-promesa'
        );

        add_settings_field(
            'menu_promesa_list_endpoint',
            __('Endpoint para obtener lista de menús', 'menu-promesa'),
            array($this, 'render_list_endpoint_field'),
            'menu-promesa',
            'menu_promesa_api_section'
        );

        add_settings_field(
            'menu_promesa_endpoint_route',
            __('Ruta del endpoint del menú', 'menu-promesa'),
            array($this, 'render_endpoint_route_field'),
            'menu-promesa',
            'menu_promesa_api_section'
        );
    }

    /**
     * Renderizar información de la sección
     */
    public function render_section_info() {
        echo '<p>' . __('Configure los endpoints de la API para los menús. La URL base será automáticamente el dominio del sitio.', 'menu-promesa') . '</p>';
        echo '<p><strong>' . __('URL Base:', 'menu-promesa') . '</strong> ' . home_url() . '</p>';
    }

    /**
     * Renderizar campo para el endpoint de lista de menús
     */
    public function render_list_endpoint_field() {
        $value = get_option('menu_promesa_list_endpoint', '/wp-json/custom/v1/obtenermenus');
        ?>
        <input type="text"
               id="menu_promesa_list_endpoint"
               name="menu_promesa_list_endpoint"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text"
               placeholder="/wp-json/custom/v1/obtenermenus">
        <p class="description">
            <?php _e('Ruta del endpoint que devuelve la lista de menús (nombre e ID). Ejemplo: /wp-json/custom/v1/obtenermenus', 'menu-promesa'); ?>
        </p>
        <?php
    }

    /**
     * Renderizar campo para la ruta del endpoint del menú
     */
    public function render_endpoint_route_field() {
        $value = get_option('menu_promesa_endpoint_route', '/wp-json/custom/v1/menus/:idMenu');
        ?>
        <input type="text"
               id="menu_promesa_endpoint_route"
               name="menu_promesa_endpoint_route"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text"
               placeholder="/wp-json/custom/v1/menus/:idMenu">
        <p class="description">
            <?php _e('Ruta del endpoint del menú. Use :idMenu como placeholder para el ID. Ejemplo: /wp-json/custom/v1/menus/:idMenu', 'menu-promesa'); ?>
        </p>
        <?php
    }

    /**
     * Renderizar página de administración
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Verificar si se guardó la configuración
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'menu_promesa_messages',
                'menu_promesa_message',
                __('Configuración guardada exitosamente', 'menu-promesa'),
                'updated'
            );
        }

        settings_errors('menu_promesa_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="menu-promesa-admin-container">
                <form action="options.php" method="post">
                    <?php
                    settings_fields('menu_promesa_settings');
                    do_settings_sections('menu-promesa');
                    submit_button(__('Guardar Configuración', 'menu-promesa'));
                    ?>
                </form>

                <div class="menu-promesa-info">
                    <h2><?php _e('Cómo usar', 'menu-promesa'); ?></h2>
                    <ol>
                        <li><?php _e('Configure los endpoints de API arriba.', 'menu-promesa'); ?></li>
                        <li><?php _e('Vaya a Apariencia > Personalizar > Widgets', 'menu-promesa'); ?></li>
                        <li><?php _e('Agregue el widget "Menu Promesa" a la ubicación deseada', 'menu-promesa'); ?></li>
                        <li><?php _e('Seleccione el menú del dropdown en el widget', 'menu-promesa'); ?></li>
                        <li><?php _e('Publique los cambios', 'menu-promesa'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Encolar scripts y estilos de administración
     */
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_menu-promesa' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'menu-promesa-admin',
            MENU_PROMESA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            MENU_PROMESA_VERSION
        );
    }
}

// Inicializar la clase de administración
new Menu_Promesa_Admin();
