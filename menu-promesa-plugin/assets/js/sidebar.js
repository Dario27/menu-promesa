/**
 * JavaScript para el Sidebar Lateral
 * Menu Promesa - WordPress Plugin
 */

(function($) {
    'use strict';

    // Configuración
    var config = {
        mobileBreakpoint: 768,
        storageKey: 'menuPromesaSidebarState'
    };

    /**
     * Inicializar el sidebar
     */
    function initSidebar() {
        var $sidebar = $('#menu-promesa-sidebar');

        if (!$sidebar.length) {
            return;
        }

        var position = menuPromesaSidebarData.position || 'left';
        var menuId = menuPromesaSidebarData.menuId;

        // Ajustar el body en desktop
        adjustBodyForSidebar(position);

        // Cargar el menú
        if (menuId) {
            loadSidebarMenu($sidebar, menuId);
        }

        // Inicializar controles
        initSidebarControls($sidebar);

        // Manejar resize
        handleResize($sidebar, position);

        // Restaurar estado en móvil (opcional)
        // restoreSidebarState($sidebar);
    }

    /**
     * Ajustar el body para el sidebar en desktop
     */
    function adjustBodyForSidebar(position) {
        if (!isMobile()) {
            $('body').addClass('menu-promesa-has-sidebar-' + position);
        }
    }

    /**
     * Cargar menú del sidebar desde la API
     */
    function loadSidebarMenu($sidebar, menuId) {
        var $menuContainer = $sidebar.find('.menu-promesa-sidebar-menu');
        var $loading = $menuContainer.find('.menu-promesa-loading');
        var $content = $menuContainer.find('.menu-promesa-content');
        var $error = $menuContainer.find('.menu-promesa-error');

        // Mostrar loading
        $loading.show();
        $content.hide();
        $error.hide();

        // Construir URL
        var endpointRoute = menuPromesaSidebarData.endpointRoute.replace(':idMenu', menuId);
        var url = menuPromesaSidebarData.baseUrl + endpointRoute;

        // Hacer petición AJAX
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                $loading.hide();

                if (response && (response.menu || response.items || Array.isArray(response))) {
                    // Usar la función de renderizado del frontend.js
                    var html = renderSidebarMenu(response);
                    $content.html(html).fadeIn(300);

                    // Inicializar interactividad del menú
                    initSidebarMenuInteractivity($content, $sidebar);
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
     * Renderizar menú para sidebar (siempre vertical)
     */
    function renderSidebarMenu(data) {
        var items = [];

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
     * Renderizar items del menú (copia de frontend.js)
     */
    function renderMenuItems(items, level) {
        var html = '';

        if (level > 3) {
            return html;
        }

        $.each(items, function(index, item) {
            var hasChildren = item.children && Array.isArray(item.children) && item.children.length > 0;
            var itemClass = 'menu-promesa-item menu-promesa-level-' + level;

            if (hasChildren) {
                itemClass += ' menu-promesa-item-has-children';
            }

            html += '<li class="' + itemClass + '" data-level="' + level + '">';

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
     * Inicializar interactividad del menú en sidebar
     */
    function initSidebarMenuInteractivity($content, $sidebar) {
        var $nav = $content.find('.menu-promesa-nav');

        // Marcar item activo
        markActiveItem($nav);

        // Siempre usar comportamiento tipo acordeón en sidebar (incluso en desktop)
        initAccordionBehavior($nav);

        // Cerrar sidebar al hacer clic en un link (solo en móvil)
        if (isMobile()) {
            $nav.find('a.menu-promesa-link').on('click', function() {
                closeSidebar($sidebar);
            });
        }
    }

    /**
     * Comportamiento de acordeón para el menú en sidebar
     */
    function initAccordionBehavior($nav) {
        $nav.on('click', '.menu-promesa-item-has-children > a, .menu-promesa-item-has-children > span', function(e) {
            var $link = $(this);
            var $item = $link.parent();
            var $submenu = $item.children('.menu-promesa-submenu');

            if ($submenu.length > 0) {
                e.preventDefault();
                e.stopPropagation();

                // Cerrar hermanos
                $item.siblings('.menu-promesa-item-open').each(function() {
                    $(this).removeClass('menu-promesa-item-open')
                           .children('.menu-promesa-submenu').slideUp(300);
                });

                // Toggle actual
                $item.toggleClass('menu-promesa-item-open');
                $submenu.slideToggle(300);
            }
        });
    }

    /**
     * Marcar item activo
     */
    function markActiveItem($nav) {
        var currentUrl = window.location.href;
        var currentPath = window.location.pathname;

        $nav.find('a.menu-promesa-link').each(function() {
            var $link = $(this);
            var href = $link.attr('href');

            if (href === currentUrl || href === currentPath) {
                $link.closest('.menu-promesa-item').addClass('active');

                // Abrir padres
                $link.closest('.menu-promesa-item').parents('.menu-promesa-item-has-children').each(function() {
                    $(this).addClass('menu-promesa-item-open')
                           .children('.menu-promesa-submenu').show();
                });
            }
        });
    }

    /**
     * Inicializar controles del sidebar (toggle, close, overlay)
     */
    function initSidebarControls($sidebar) {
        var $toggle = $('.menu-promesa-sidebar-toggle');
        var $close = $('.menu-promesa-sidebar-close');
        var $overlay = $('.menu-promesa-sidebar-overlay');

        // Toggle button
        $toggle.on('click', function(e) {
            e.preventDefault();
            toggleSidebar($sidebar);
        });

        // Close button
        $close.on('click', function(e) {
            e.preventDefault();
            closeSidebar($sidebar);
        });

        // Overlay click
        $overlay.on('click', function() {
            closeSidebar($sidebar);
        });

        // ESC key para cerrar
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $sidebar.hasClass('sidebar-open')) {
                closeSidebar($sidebar);
            }
        });
    }

    /**
     * Toggle del sidebar
     */
    function toggleSidebar($sidebar) {
        if ($sidebar.hasClass('sidebar-open')) {
            closeSidebar($sidebar);
        } else {
            openSidebar($sidebar);
        }
    }

    /**
     * Abrir sidebar
     */
    function openSidebar($sidebar) {
        $sidebar.addClass('sidebar-open');
        $('body').addClass('sidebar-is-open');

        // Prevenir scroll del body en móvil
        if (isMobile()) {
            $('body').css('overflow', 'hidden');
        }

        // Guardar estado
        saveSidebarState(true);
    }

    /**
     * Cerrar sidebar
     */
    function closeSidebar($sidebar) {
        $sidebar.removeClass('sidebar-open');
        $('body').removeClass('sidebar-is-open');

        // Restaurar scroll
        if (isMobile()) {
            $('body').css('overflow', '');
        }

        // Guardar estado
        saveSidebarState(false);
    }

    /**
     * Manejar resize de ventana
     */
    function handleResize($sidebar, position) {
        var resizeTimer;

        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // En desktop, remover clase de abierto y ajustar body
                if (!isMobile()) {
                    $sidebar.removeClass('sidebar-open');
                    $('body').addClass('menu-promesa-has-sidebar-' + position)
                             .removeClass('sidebar-is-open')
                             .css('overflow', '');
                } else {
                    // En móvil, remover clase del body
                    $('body').removeClass('menu-promesa-has-sidebar-left menu-promesa-has-sidebar-right');
                }
            }, 250);
        });
    }

    /**
     * Guardar estado del sidebar
     */
    function saveSidebarState(isOpen) {
        if (typeof(Storage) !== "undefined") {
            localStorage.setItem(config.storageKey, isOpen ? '1' : '0');
        }
    }

    /**
     * Restaurar estado del sidebar
     */
    function restoreSidebarState($sidebar) {
        if (typeof(Storage) !== "undefined" && isMobile()) {
            var state = localStorage.getItem(config.storageKey);
            if (state === '1') {
                openSidebar($sidebar);
            }
        }
    }

    /**
     * Mostrar error
     */
    function showError($errorContainer, message) {
        $errorContainer
            .html('<p class="menu-promesa-error-message">' + escapeHtml(message) + '</p>')
            .fadeIn(300);
    }

    /**
     * Escape HTML
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
     * Detectar si es móvil
     */
    function isMobile() {
        return window.innerWidth <= config.mobileBreakpoint;
    }

    /**
     * Inicializar cuando el documento esté listo
     */
    $(document).ready(function() {
        initSidebar();
    });

})(jQuery);
