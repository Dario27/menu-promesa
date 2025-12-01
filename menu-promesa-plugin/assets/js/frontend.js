/**
 * JavaScript para cargar menús dinámicamente desde la API
 * Soporte para menú multinivel interactivo (3 niveles)
 */

(function($) {
    'use strict';

    // Configuración global
    var config = {
        mobileBreakpoint: 768,
        animationDuration: 300,
        hoverDelay: 200
    };

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
                    $content.html(html).fadeIn(config.animationDuration);

                    // Inicializar interactividad después de renderizar
                    initMenuInteractivity($content);
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
               renderMenuItems(items, 1) +
               '</ul></nav>';
    }

    /**
     * Renderizar items del menú recursivamente con soporte para 3 niveles
     * @param {Array} items - Items del menú
     * @param {Number} level - Nivel actual (1, 2 o 3)
     */
    function renderMenuItems(items, level) {
        var html = '';

        // Limitar a 3 niveles
        if (level > 3) {
            return html;
        }

        $.each(items, function(index, item) {
            var hasChildren = item.children && Array.isArray(item.children) && item.children.length > 0;
            var itemClass = 'menu-promesa-item menu-promesa-level-' + level;

            if (hasChildren) {
                itemClass += ' menu-promesa-item-has-children';
            }

            // Construir el elemento del menú
            html += '<li class="' + itemClass + '" data-level="' + level + '">';

            // Link del menú
            if (item.url || item.link) {
                var url = item.url || item.link;
                var title = item.title || item.name || item.label || 'Sin título';
                var target = item.target || '_self';

                html += '<a href="' + escapeHtml(url) + '" target="' + escapeHtml(target) + '" class="menu-promesa-link">';
                html += escapeHtml(title);
                html += '</a>';
            } else {
                var title = item.title || item.name || item.label || 'Sin título';
                html += '<span class="menu-promesa-link">' + escapeHtml(title) + '</span>';
            }

            // Renderizar hijos si existen y no superamos el nivel 3
            if (hasChildren && level < 3) {
                html += '<ul class="menu-promesa-submenu menu-promesa-submenu-level-' + (level + 1) + '">';
                html += renderMenuItems(item.children, level + 1);
                html += '</ul>';
            }

            html += '</li>';
        });

        return html;
    }

    /**
     * Inicializar interactividad del menú
     */
    function initMenuInteractivity($content) {
        var $nav = $content.find('.menu-promesa-nav');

        // Marcar item activo basado en la URL actual
        markActiveItem($nav);

        // Inicializar comportamiento según dispositivo
        if (isMobile()) {
            initMobileBehavior($nav);
        } else {
            initDesktopBehavior($nav);
        }

        // Reinicializar en cambio de tamaño de ventana
        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (isMobile()) {
                    initMobileBehavior($nav);
                } else {
                    initDesktopBehavior($nav);
                }
            }, 250);
        });
    }

    /**
     * Comportamiento para dispositivos móviles
     */
    function initMobileBehavior($nav) {
        // Remover eventos anteriores
        $nav.find('.menu-promesa-item-has-children > a, .menu-promesa-item-has-children > span').off('click.menuPromesa');

        // Agregar click para toggle de submenús
        $nav.on('click.menuPromesa', '.menu-promesa-item-has-children > a, .menu-promesa-item-has-children > span', function(e) {
            var $link = $(this);
            var $item = $link.parent();
            var $submenu = $item.children('.menu-promesa-submenu');

            // Si tiene hijos, prevenir navegación y hacer toggle
            if ($submenu.length > 0) {
                e.preventDefault();
                e.stopPropagation();

                // Cerrar otros submenús del mismo nivel
                $item.siblings('.menu-promesa-item-open').each(function() {
                    $(this).removeClass('menu-promesa-item-open')
                           .children('.menu-promesa-submenu').slideUp(config.animationDuration);
                });

                // Toggle del submenú actual
                $item.toggleClass('menu-promesa-item-open');
                $submenu.slideToggle(config.animationDuration);
            }
        });
    }

    /**
     * Comportamiento para desktop
     */
    function initDesktopBehavior($nav) {
        // Remover eventos de móvil
        $nav.off('click.menuPromesa');

        // Cerrar submenús abiertos en móvil
        $nav.find('.menu-promesa-item-open').removeClass('menu-promesa-item-open');
        $nav.find('.menu-promesa-submenu').removeAttr('style');

        // Hover para nivel 1
        $nav.find('.menu-promesa-menu > .menu-promesa-item-has-children').hover(
            function() {
                $(this).addClass('menu-promesa-item-hover');
            },
            function() {
                $(this).removeClass('menu-promesa-item-hover');
            }
        );

        // Hover para niveles 2 y 3
        $nav.find('.menu-promesa-submenu .menu-promesa-item-has-children').hover(
            function() {
                $(this).addClass('menu-promesa-item-hover');
            },
            function() {
                $(this).removeClass('menu-promesa-item-hover');
            }
        );
    }

    /**
     * Marcar item activo basado en URL
     */
    function markActiveItem($nav) {
        var currentUrl = window.location.href;
        var currentPath = window.location.pathname;

        $nav.find('a.menu-promesa-link').each(function() {
            var $link = $(this);
            var href = $link.attr('href');

            if (href === currentUrl || href === currentPath) {
                $link.closest('.menu-promesa-item').addClass('active');

                // Abrir padres en móvil
                if (isMobile()) {
                    $link.closest('.menu-promesa-item').parents('.menu-promesa-item-has-children').each(function() {
                        $(this).addClass('menu-promesa-item-open')
                               .children('.menu-promesa-submenu').show();
                    });
                }
            }
        });
    }

    /**
     * Mostrar mensaje de error
     */
    function showError($errorContainer, message) {
        $errorContainer
            .html('<p class="menu-promesa-error-message">' + escapeHtml(message) + '</p>')
            .fadeIn(config.animationDuration);
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
     * Detectar si es dispositivo móvil
     */
    function isMobile() {
        return window.innerWidth <= config.mobileBreakpoint;
    }

    /**
     * Inicializar cuando el documento esté listo
     */
    $(document).ready(function() {
        initMenuLoader();
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
