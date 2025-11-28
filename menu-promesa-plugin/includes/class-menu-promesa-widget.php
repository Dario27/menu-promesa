<?php
/**
 * Widget para mostrar menús desde API
 */

if (!defined('ABSPATH')) {
    exit;
}

class Menu_Promesa_Widget extends WP_Widget {

    /**
     * Constructor del widget
     */
    public function __construct() {
        parent::__construct(
            'menu_promesa_widget',
            __('Menu Promesa', 'menu-promesa'),
            array(
                'description' => __('Muestra un menú cargado dinámicamente desde una API', 'menu-promesa'),
                'customize_selective_refresh' => true,
            )
        );

        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Encolar scripts necesarios
     */
    public function enqueue_scripts() {
        if (is_active_widget(false, false, $this->id_base)) {
            wp_enqueue_style(
                'menu-promesa-frontend',
                MENU_PROMESA_PLUGIN_URL . 'assets/css/frontend.css',
                array(),
                MENU_PROMESA_VERSION
            );

            wp_enqueue_script(
                'menu-promesa-frontend',
                MENU_PROMESA_PLUGIN_URL . 'assets/js/frontend.js',
                array('jquery'),
                MENU_PROMESA_VERSION,
                true
            );

            // Pasar datos al JavaScript
            wp_localize_script('menu-promesa-frontend', 'menuPromesaData', array(
                'baseUrl' => home_url(),
                'endpointRoute' => get_option('menu_promesa_endpoint_route', '/wp-json/custom/v1/menus/:idMenu'),
            ));
        }
    }

    /**
     * Formulario del widget en el backend
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $menu_id = !empty($instance['menu_id']) ? $instance['menu_id'] : '';

        // Obtener la lista de menús
        $menus = $this->get_menus_list();
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('Título:', 'menu-promesa'); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('menu_id')); ?>">
                <?php _e('Seleccionar Menú:', 'menu-promesa'); ?>
            </label>
            <select class="widefat menu-promesa-menu-select"
                    id="<?php echo esc_attr($this->get_field_id('menu_id')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('menu_id')); ?>"
                    data-widget-id="<?php echo esc_attr($this->id); ?>">
                <option value=""><?php _e('-- Seleccionar --', 'menu-promesa'); ?></option>
                <?php if (!empty($menus) && !is_wp_error($menus)): ?>
                    <?php foreach ($menus as $menu): ?>
                        <option value="<?php echo esc_attr($menu['id']); ?>" <?php selected($menu_id, $menu['id']); ?>>
                            <?php echo esc_html($menu['name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </p>

        <?php if (is_wp_error($menus)): ?>
            <p class="description" style="color: #dc3232;">
                <?php _e('Error al cargar los menús. Verifique la configuración del endpoint.', 'menu-promesa'); ?>
                <br>
                <small><?php echo esc_html($menus->get_error_message()); ?></small>
            </p>
        <?php endif; ?>

        <p class="description">
            <a href="<?php echo admin_url('admin.php?page=menu-promesa-settings'); ?>">
                <?php _e('Configurar endpoints de API', 'menu-promesa'); ?>
            </a>
        </p>

        <script>
        jQuery(document).ready(function($) {
            // Recargar menús cuando se actualice el widget
            $(document).on('widget-updated widget-added', function(e, widget) {
                if (widget && widget.find('.menu-promesa-menu-select').length) {
                    // Recargar la lista de menús
                    var $select = widget.find('.menu-promesa-menu-select');
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'menu_promesa_get_menus'
                        },
                        success: function(response) {
                            if (response.success && response.data) {
                                var currentValue = $select.val();
                                $select.empty().append('<option value="">-- Seleccionar --</option>');
                                $.each(response.data, function(i, menu) {
                                    $select.append(
                                        $('<option></option>')
                                            .val(menu.id)
                                            .text(menu.name)
                                            .prop('selected', menu.id == currentValue)
                                    );
                                });
                            }
                        }
                    });
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Actualizar widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['menu_id'] = (!empty($new_instance['menu_id'])) ? sanitize_text_field($new_instance['menu_id']) : '';

        return $instance;
    }

    /**
     * Mostrar widget en el frontend
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $menu_id = !empty($instance['menu_id']) ? $instance['menu_id'] : '';

        if (empty($menu_id)) {
            return;
        }

        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        // Contenedor para el menú
        echo '<div class="menu-promesa-container" data-menu-id="' . esc_attr($menu_id) . '">';
        echo '<div class="menu-promesa-loading">' . __('Cargando menú...', 'menu-promesa') . '</div>';
        echo '<div class="menu-promesa-content" style="display:none;"></div>';
        echo '<div class="menu-promesa-error" style="display:none;"></div>';
        echo '</div>';

        echo $args['after_widget'];
    }

    /**
     * Obtener lista de menús desde WordPress
     */
    private function get_menus_list() {
        $menus = wp_get_nav_menus();

        if (empty($menus)) {
            return array();
        }

        // Formatear los menús al formato esperado por el widget
        $formatted_menus = array();
        foreach ($menus as $menu) {
            $formatted_menus[] = array(
                'id' => $menu->term_id,
                'name' => $menu->name,
            );
        }

        return $formatted_menus;
    }
}

/**
 * AJAX para obtener menús
 */
add_action('wp_ajax_menu_promesa_get_menus', 'menu_promesa_ajax_get_menus');
function menu_promesa_ajax_get_menus() {
    $menus = wp_get_nav_menus();

    if (empty($menus)) {
        wp_send_json_success(array());
        return;
    }

    // Formatear los menús al formato esperado
    $formatted_menus = array();
    foreach ($menus as $menu) {
        $formatted_menus[] = array(
            'id' => $menu->term_id,
            'name' => $menu->name,
        );
    }

    wp_send_json_success($formatted_menus);
}
