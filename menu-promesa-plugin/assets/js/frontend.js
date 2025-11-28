/**
 * JavaScript para cargar menús dinámicamente desde la API
 */

(function($) {
    'use strict';

    /**
     * Inicializar el cargador de menús
     */
    function initMenuLoader() {
        $('.menu-promesa-container').each(function() {
            var $container = $(this);
            var menuId = $container.data('menu-id');

            if (menuId) {
                loadMenu($container, menuId);
            }
        });
    }

    /**
     * Cargar menú desde la API
     */
    function loadMenu($container, menuId) {
        var $loading = $container.find('.menu-promesa-loading');
        var $content = $container.find('.menu-promesa-content');
        var $error = $container.find('.menu-promesa-error');

        // Mostrar loading
        $loading.show();
        $content.hide();
        $error.hide();

        // Construir la URL del endpoint
        var endpointRoute = menuPromesaData.endpointRoute.replace(':idMenu', menuId);
        var url = menuPromesaData.baseUrl + endpointRoute;

        // Hacer la petición a la API
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                $loading.hide();

                if (response && (response.menu || response.items || Array.isArray(response))) {
                    // Renderizar el menú
                    var html = renderMenu(response);
                    $content.html(html).fadeIn();
                } else {
                    showError($error, 'No se encontraron datos del menú');
                }
            },
            error: function(xhr, status, error) {
                $loading.hide();

                var errorMessage = 'Error al cargar el menú';

                if (status === 'timeout') {
                    errorMessage = 'La petición ha excedido el tiempo de espera';
                } else if (xhr.status === 404) {
                    errorMessage = 'El menú no fue encontrado';
                } else if (xhr.status === 0) {
                    errorMessage = 'No se pudo conectar con el servidor';
                }

                showError($error, errorMessage);
            }
        });
    }

    /**
     * Renderizar el menú HTML
     */
    function renderMenu(data) {
        var items = [];

        // Determinar la estructura de datos
        if (data.menu && Array.isArray(data.menu)) {
            items = data.menu;
        } else if (data.items && Array.isArray(data.items)) {
            items = data.items;
        } else if (Array.isArray(data)) {
            items = data;
        }

        if (!items.length) {
            return '<p class="menu-promesa-empty">No hay elementos en el menú</p>';
        }

        return '<nav class="menu-promesa-nav"><ul class="menu-promesa-menu">' +
               renderMenuItems(items) +
               '</ul></nav>';
    }

    /**
     * Renderizar items del menú recursivamente
     */
    function renderMenuItems(items, isChild) {
        var html = '';
        var className = isChild ? 'menu-promesa-submenu' : 'menu-promesa-menu';

        $.each(items, function(index, item) {
            var hasChildren = item.children && Array.isArray(item.children) && item.children.length > 0;
            var itemClass = 'menu-promesa-item';

            if (hasChildren) {
                itemClass += ' menu-promesa-item-has-children';
            }

            // Construir el elemento del menú
            html += '<li class="' + itemClass + '">';

            // Link del menú
            if (item.url || item.link) {
                var url = item.url || item.link;
                var title = item.title || item.name || item.label || 'Sin título';
                var target = item.target || '_self';

                html += '<a href="' + escapeHtml(url) + '" target="' + escapeHtml(target) + '">';
                html += escapeHtml(title);
                html += '</a>';
            } else {
                html += '<span>' + escapeHtml(item.title || item.name || 'Sin título') + '</span>';
            }

            // Renderizar hijos si existen
            if (hasChildren) {
                html += '<ul class="menu-promesa-submenu">';
                html += renderMenuItems(item.children, true);
                html += '</ul>';
            }

            html += '</li>';
        });

        return html;
    }

    /**
     * Mostrar mensaje de error
     */
    function showError($errorContainer, message) {
        $errorContainer
            .html('<p class="menu-promesa-error-message">' + escapeHtml(message) + '</p>')
            .fadeIn();
    }

    /**
     * Escapar HTML para prevenir XSS
     */
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Toggle para submenús (opcional)
     */
    function initSubmenuToggle() {
        $(document).on('click', '.menu-promesa-item-has-children > a', function(e) {
            var $item = $(this).parent();

            // Solo prevenir default en móviles
            if (window.innerWidth <= 768) {
                e.preventDefault();
                $item.toggleClass('menu-promesa-item-open');
                $item.find('> .menu-promesa-submenu').slideToggle(200);
            }
        });
    }

    /**
     * Inicializar cuando el documento esté listo
     */
    $(document).ready(function() {
        initMenuLoader();
        initSubmenuToggle();
    });

    /**
     * Reinicializar cuando se actualice el customizer (preview)
     */
    if (typeof wp !== 'undefined' && wp.customize && wp.customize.selectiveRefresh) {
        wp.customize.selectiveRefresh.bind('partial-content-rendered', function() {
            initMenuLoader();
        });
    }

})(jQuery);
