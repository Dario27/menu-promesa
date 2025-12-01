<?php

/**
 * Plugin Name: Menu Promesa
 * Plugin URI: https://github.com/Dario27/menu-promesa
 * Description: Plugin para gestionar menús dinámicos mediante API REST
 * Version: 1.3.0
 * Author: Steven Chilan
 * Text Domain: menu-promesa
 * Domain Path: /languages
 * Requires at least: WordPress 5.0 o superior
 * Requires PHP: 7.4 o superior
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('MENU_PROMESA_VERSION', '1.3.0');
define('MENU_PROMESA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MENU_PROMESA_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Clase principal del plugin
 */
class Menu_Promesa {

    /**
     * Instancia única del plugin
     */
    private static $instance = null;

    /**
     * Obtener instancia única del plugin
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Incluir archivos necesarios
     */
    private function includes() {
        require_once MENU_PROMESA_PLUGIN_DIR . 'admin/class-menu-promesa-admin.php';
        require_once MENU_PROMESA_PLUGIN_DIR . 'includes/class-menu-promesa-widget.php';
        require_once MENU_PROMESA_PLUGIN_DIR . 'includes/class-menu-promesa-sidebar.php';
    }

    /**
     * Inicializar hooks
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('widgets_init', array($this, 'register_widgets'));
    }

    /**
     * Cargar traducciones
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'menu-promesa',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }

    /**
     * Registrar widgets
     */
    public function register_widgets() {
        register_widget('Menu_Promesa_Widget');
    }
}

/**
 * Inicializar el plugin
 */
function menu_promesa_init() {
    return Menu_Promesa::get_instance();
}

// Iniciar el plugin
menu_promesa_init();
