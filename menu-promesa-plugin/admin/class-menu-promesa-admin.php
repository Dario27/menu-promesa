<?php

/**
 * Clase para la administración del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Menu_Promesa_Admin
{

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Agregar menú de administración
     */
    public function add_admin_menu()
    {
        // Menú principal
        add_menu_page(
            __('Menu Promesa', 'menu-promesa'),
            __('Menu Promesa', 'menu-promesa'),
            'manage_options',
            'menu-promesa',
            array($this, 'render_main_page'),
            'dashicons-menu-alt',
            30
        );

        // Submenú de configuración
        add_submenu_page(
            'menu-promesa',
            __('Configuración de API', 'menu-promesa'),
            __('Configuración de API', 'menu-promesa'),
            'manage_options',
            'menu-promesa-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Registrar configuraciones
     */
    public function register_settings()
    {
        register_setting('menu_promesa_settings', 'menu_promesa_endpoint_route');
        register_setting('menu_promesa_settings', 'menu_promesa_list_endpoint');
        register_setting('menu_promesa_settings', 'menu_promesa_enable_sidebar');
        register_setting('menu_promesa_settings', 'menu_promesa_sidebar_menu_id');
        register_setting('menu_promesa_settings', 'menu_promesa_sidebar_position');

        add_settings_section(
            'menu_promesa_api_section',
            __('Configuración de API', 'menu-promesa'),
            array($this, 'render_section_info'),
            'menu-promesa-settings'
        );

        add_settings_field(
            'menu_promesa_endpoint_route',
            __('Ruta del endpoint del menú', 'menu-promesa'),
            array($this, 'render_endpoint_route_field'),
            'menu-promesa-settings',
            'menu_promesa_api_section'
        );

        add_settings_section(
            'menu_promesa_sidebar_section',
            __('Configuración de Sidebar Automático', 'menu-promesa'),
            array($this, 'render_sidebar_section_info'),
            'menu-promesa'
        );

        add_settings_field(
            'menu_promesa_enable_sidebar',
            __('Activar sidebar automático', 'menu-promesa'),
            array($this, 'render_enable_sidebar_field'),
            'menu-promesa',
            'menu_promesa_sidebar_section'
        );

        add_settings_field(
            'menu_promesa_sidebar_menu_id',
            __('Menú a mostrar en sidebar', 'menu-promesa'),
            array($this, 'render_sidebar_menu_field'),
            'menu-promesa',
            'menu_promesa_sidebar_section'
        );

        add_settings_field(
            'menu_promesa_sidebar_position',
            __('Posición del sidebar', 'menu-promesa'),
            array($this, 'render_sidebar_position_field'),
            'menu-promesa',
            'menu_promesa_sidebar_section'
        );
    }

    /**
     * Renderizar información de la sección
     */
    public function render_section_info()
    {
        echo '<p>' . __('Configure los endpoints de la API para los menús. La URL base será automáticamente el dominio del sitio.', 'menu-promesa') . '</p>';
        echo '<p><strong>' . __('URL Base:', 'menu-promesa') . '</strong> ' . home_url() . '</p>';
    }

    /**
     * Renderizar campo para la ruta del endpoint del menú
     */
    public function render_endpoint_route_field()
    {
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
     * Renderizar página principal
     */
    public function render_main_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $menus = wp_get_nav_menus();
    ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="menu-promesa-info">
                <h2><?php _e('Cómo usar', 'menu-promesa'); ?></h2>
                <ol>
                    <li><?php _e('Cree o gestione sus menús desde Apariencia > Menús', 'menu-promesa'); ?></li>
                    <li><?php _e('Configure los endpoints de API en Configuración de API', 'menu-promesa'); ?></li>
                    <li><?php _e('Vaya a Apariencia > Personalizar > Widgets', 'menu-promesa'); ?></li>
                    <li><?php _e('Agregue el widget "Menu Promesa" a la ubicación deseada', 'menu-promesa'); ?></li>
                    <li><?php _e('Seleccione el menú del dropdown en el widget', 'menu-promesa'); ?></li>
                    <li><?php _e('Publique los cambios', 'menu-promesa'); ?></li>
                </ol>
            </div>

            <div class="menu-promesa-menus">
                <h2><?php _e('Menús disponibles', 'menu-promesa'); ?></h2>
                <?php if (!empty($menus)): ?>
                    <?php
                    // Obtener las ubicaciones de menú registradas y sus menús asignados
                    $locations = get_theme_mod('nav_menu_locations');
                    ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('ID', 'menu-promesa'); ?></th>
                                <th><?php _e('Nombre del Menú', 'menu-promesa'); ?></th>
                                <th><?php _e('Ubicaciones', 'menu-promesa'); ?></th>
                                <th><?php _e('Activo', 'menu-promesa'); ?></th>
                                <th><?php _e('Estado', 'menu-promesa'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menus as $menu): ?>
                                <?php
                                // Verificar si el menú está asignado a alguna ubicación
                                $is_active = false;
                                $menu_locations = array();

                                if (!empty($locations)) {
                                    foreach ($locations as $location => $menu_id) {
                                        if ($menu_id == $menu->term_id) {
                                            $is_active = true;
                                            $menu_locations[] = $location;
                                        }
                                    }
                                }

                                // Obtener el conteo de items del menú
                                $menu_items = wp_get_nav_menu_items($menu->term_id);
                                $item_count = is_array($menu_items) ? count($menu_items) : 0;

                                // Determinar el estado
                                $status = $is_active ? __('Publicado', 'menu-promesa') : __('Inactivo', 'menu-promesa');
                                $status_class = $is_active ? 'status-active' : 'status-inactive';
                                ?>
                                <tr>
                                    <td><?php echo esc_html($menu->term_id); ?></td>
                                    <td>
                                        <strong><?php echo esc_html($menu->name); ?></strong>
                                        <br>
                                        <small><?php printf(__('%d elementos', 'menu-promesa'), $item_count); ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($menu_locations)) {
                                            echo esc_html(implode(', ', $menu_locations));
                                        } else {
                                            echo '—';
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align: center; font-size: 18px;">
                                        <?php echo $is_active ? '✅' : '—'; ?>
                                    </td>
                                    <td>
                                        <span class="<?php echo esc_attr($status_class); ?>" style="
                                            display: inline-block;
                                            padding: 3px 10px;
                                            border-radius: 3px;
                                            font-size: 12px;
                                             <?php echo $is_active
                                             ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;'
                                             : 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'; ?>
                                            ">
                                            <?php echo esc_html($status); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p><?php _e('No hay menús creados. Vaya a Apariencia > Menús para crear uno.', 'menu-promesa'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php
    }

    /**
     * Renderizar información de la sección sidebar
     */
    public function render_sidebar_section_info() {
        echo '<p>' . __('Configure el sidebar lateral que aparecerá automáticamente en todas las páginas de tu sitio.', 'menu-promesa') . '</p>';
    }

    /**
     * Renderizar campo para activar sidebar
     */
    public function render_enable_sidebar_field() {
        $value = get_option('menu_promesa_enable_sidebar', '0');
        ?>
        <label>
            <input type="checkbox"
                   id="menu_promesa_enable_sidebar"
                   name="menu_promesa_enable_sidebar"
                   value="1"
                   <?php checked($value, '1'); ?>>
            <?php _e('Mostrar menú en sidebar lateral en todas las páginas', 'menu-promesa'); ?>
        </label>
        <p class="description">
            <?php _e('Al activar esta opción, el menú se mostrará automáticamente en un sidebar lateral fijo en todas las páginas del sitio.', 'menu-promesa'); ?>
        </p>
        <?php
    }

    /**
     * Renderizar campo para seleccionar menú del sidebar
     */
    public function render_sidebar_menu_field() {
        $selected_menu_id = get_option('menu_promesa_sidebar_menu_id', '');

        // Obtener lista de menús
        $list_endpoint = get_option('menu_promesa_list_endpoint', '/wp-json/custom/v1/obtenermenus');
        $url = home_url($list_endpoint);

        $response = wp_remote_get($url, array(
            'timeout' => 15,
            'sslverify' => false,
        ));

        $menus = array();
        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                $menus = $data;
            }
        }
        ?>
        <select id="menu_promesa_sidebar_menu_id"
                name="menu_promesa_sidebar_menu_id"
                class="regular-text">
            <option value=""><?php _e('-- Seleccionar menú --', 'menu-promesa'); ?></option>
            <?php foreach ($menus as $menu): ?>
                <option value="<?php echo esc_attr($menu['id']); ?>"
                        <?php selected($selected_menu_id, $menu['id']); ?>>
                    <?php echo esc_html($menu['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">
            <?php _e('Selecciona el menú que se mostrará en el sidebar lateral.', 'menu-promesa'); ?>
        </p>
        <?php
    }

    /**
     * Renderizar campo para posición del sidebar
     */
    public function render_sidebar_position_field() {
        $value = get_option('menu_promesa_sidebar_position', 'left');
        ?>
        <select id="menu_promesa_sidebar_position"
                name="menu_promesa_sidebar_position"
                class="regular-text">
            <option value="left" <?php selected($value, 'left'); ?>>
                <?php _e('Izquierda', 'menu-promesa'); ?>
            </option>
            <option value="right" <?php selected($value, 'right'); ?>>
                <?php _e('Derecha', 'menu-promesa'); ?>
            </option>
        </select>
        <p class="description">
            <?php _e('Elige en qué lado de la pantalla aparecerá el sidebar.', 'menu-promesa'); ?>
        </p>
        <?php
    }

    /**
     * Renderizar página de administración
     */
    public function render_settings_page()
    {
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
                    do_settings_sections('menu-promesa-settings');
                    submit_button(__('Guardar Configuración', 'menu-promesa'));
                    ?>
                </form>
            </div>
        </div>
<?php
    }

    /**
     * Encolar scripts y estilos de administración
     */
    public function enqueue_admin_scripts($hook)
    {
        if ('toplevel_page_menu-promesa' !== $hook && 'menu-promesa_page_menu-promesa-settings' !== $hook) {
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
