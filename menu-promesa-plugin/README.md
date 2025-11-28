# Menu Promesa - WordPress Plugin

Plugin de WordPress para la creación y gestión de menús dinámicos mediante API REST.

## Descripción

Menu Promesa es un plugin que permite cargar menús dinámicamente desde una API REST personalizada. El plugin incluye una interfaz de administración para configurar los endpoints y un widget que se integra con el Customizer de WordPress.

## Características

- ✅ Integración con el sistema nativo de menús de WordPress
- ✅ Configuración dinámica de endpoints de API
- ✅ URL base automática basada en el dominio del sitio
- ✅ Widget compatible con el Customizer de WordPress
- ✅ Carga asíncrona de menús desde API REST
- ✅ Soporte para menús multinivel (submenús)
- ✅ Diseño responsive (desktop y móvil)
- ✅ Manejo de errores y estados de carga
- ✅ Sin necesidad de shortcodes

## Instalación

1. Copie la carpeta `menu-promesa-plugin` al directorio `wp-content/plugins/` de su instalación de WordPress
2. Active el plugin desde el panel de administración de WordPress (Plugins > Plugins Instalados)
3. Configure los endpoints en el menú "Menu Promesa" del panel de administración

## Configuración

### 1. Crear o Gestionar Menús

1. Vaya a **Apariencia > Menús** en el panel de administración de WordPress
2. Cree un nuevo menú o edite uno existente
3. Agregue los elementos que desee al menú
4. Guarde los cambios

**Nota**: Los menús se gestionan desde la interfaz nativa de WordPress. El plugin lee automáticamente todos los menús disponibles.

### 2. Configurar Endpoints de API

Vaya a **Menu Promesa > Configuración de API** en el menú de administración:

- **Ruta del endpoint del menú**: Ruta para obtener el menú completo desde la API externa
  - Ejemplo: `/wp-json/custom/v1/menus/:idMenu`
  - Use `:idMenu` como placeholder para el ID del menú
  - La URL base será automáticamente el dominio de su sitio

### 3. Agregar el Widget

1. Vaya a **Apariencia > Personalizar**
2. Navegue a **Widgets**
3. Seleccione la ubicación donde desea agregar el menú
4. Haga clic en **Agregar un widget**
5. Seleccione **Menu Promesa**
6. Configure el widget:
   - **Título**: (Opcional) Título que se mostrará encima del menú
   - **Seleccionar Menú**: Elija el menú del dropdown (los menús disponibles son los creados en Apariencia > Menús)
7. Haga clic en **Publicar** para guardar los cambios

## Formato de Respuesta de la API

### Endpoint del Menú

Debe devolver un objeto con la estructura del menú. El plugin soporta múltiples formatos:

**Formato 1: Con propiedad "menu"**
```json
{
  "menu": [
    {
      "title": "Inicio",
      "url": "https://ejemplo.com",
      "target": "_self",
      "children": []
    },
    {
      "title": "Servicios",
      "url": "https://ejemplo.com/servicios",
      "target": "_self",
      "children": [
        {
          "title": "Servicio 1",
          "url": "https://ejemplo.com/servicios/servicio-1",
          "target": "_self"
        }
      ]
    }
  ]
}
```

**Formato 2: Con propiedad "items"**
```json
{
  "items": [
    {
      "name": "Inicio",
      "link": "https://ejemplo.com"
    }
  ]
}
```

**Formato 3: Array directo**
```json
[
  {
    "title": "Inicio",
    "url": "https://ejemplo.com"
  }
]
```

### Propiedades Soportadas

El plugin soporta las siguientes propiedades para cada item del menú:

- `title` / `name` / `label`: Texto del enlace
- `url` / `link`: URL del enlace
- `target`: Target del enlace (_self, _blank, etc.)
- `children`: Array de subitems (para submenús)

## Estructura de Archivos

```
menu-promesa-plugin/
├── menu-promesa.php              # Archivo principal del plugin
├── README.md                      # Este archivo
├── admin/
│   └── class-menu-promesa-admin.php  # Clase de administración
├── includes/
│   └── class-menu-promesa-widget.php # Widget del menú
└── assets/
    ├── js/
    │   └── frontend.js           # JavaScript del frontend
    └── css/
        ├── admin.css             # Estilos del admin
        └── frontend.css          # Estilos del frontend
```

## Personalización

### Estilos CSS

Puede personalizar los estilos del menú editando el archivo `assets/css/frontend.css` o agregando CSS personalizado en su tema.

Clases CSS disponibles:
- `.menu-promesa-container`: Contenedor principal
- `.menu-promesa-nav`: Elemento de navegación
- `.menu-promesa-menu`: Lista del menú
- `.menu-promesa-item`: Item del menú
- `.menu-promesa-submenu`: Submenú
- `.menu-promesa-item-has-children`: Item con hijos

### Tema Oscuro

Para activar el tema oscuro, agregue la clase `dark-theme` al contenedor:

```php
add_filter('widget_display_callback', function($instance, $widget, $args) {
    if ($widget->id_base === 'menu_promesa_widget') {
        // Modificar args para agregar clase
    }
    return $instance;
}, 10, 3);
```

## Requisitos

- WordPress 5.0 o superior
- PHP 7.0 o superior
- jQuery (incluido en WordPress)

## Soporte

Para reportar problemas o solicitar características, visite:
https://github.com/Dario27/menu-promesa/issues

## Licencia

Este plugin es software libre.

## Autor

**Dario27**

## Changelog

### 1.1.0
- Integración con el sistema nativo de menús de WordPress
- Reorganización del menú de administración con submenús
- Eliminación del endpoint de lista de menús (ahora se obtienen directamente desde WordPress)
- Los menús se gestionan desde Apariencia > Menús
- Mejoras en la interfaz de administración

### 1.0.0
- Versión inicial
- Configuración de endpoints dinámicos
- Widget para el Customizer
- Soporte para menús multinivel
- Diseño responsive
