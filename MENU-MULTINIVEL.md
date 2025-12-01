# Menú Multinivel Interactivo - Menu Promesa

## Descripción

El plugin Menu Promesa ahora incluye un sistema de menú multinivel interactivo y moderno que soporta hasta **3 niveles de profundidad**.

## Características Principales

### 🎨 Diseño Moderno e Interactivo
- **Colores personalizables** mediante variables CSS
- **Animaciones suaves** y transiciones fluidas
- **Efectos hover modernos** con gradientes
- **Sombras y bordes redondeados** para un aspecto profesional
- **Tema oscuro** incluido (opcional)

### 📱 Responsive Design
- **Desktop**: Menú horizontal con dropdowns
  - Nivel 1: Horizontal en la parte superior
  - Nivel 2: Dropdown vertical debajo del nivel 1
  - Nivel 3: Dropdown lateral a la derecha del nivel 2

- **Móvil**: Menú vertical con acordeón
  - Todos los niveles se despliegan verticalmente
  - Sistema de acordeón colapsable
  - Toggle suave con animaciones

### 🎯 Soporte para 3 Niveles

#### Estructura de Niveles
```
Nivel 1: Tecnología
  ├─ Nivel 2: Audio
  │    ├─ Nivel 3: Accesorios
  │    ├─ Nivel 3: Accesorios de DJ
  │    ├─ Nivel 3: Audífonos
  │    ├─ Nivel 3: Barras de Sonido
  │    └─ Nivel 3: ...más items
  ├─ Nivel 2: TV y Video
  └─ Nivel 2: Computación
```

### ✨ Características Interactivas

1. **Indicadores Visuales**
   - Flechas animadas para items con hijos
   - Iconos diferentes para cada nivel
   - Colores distintivos por nivel
   - Barra lateral en hover

2. **Navegación Intuitiva**
   - Detección automática de página activa
   - Apertura automática de padres del item activo
   - Cierre automático de otros submenús
   - Soporte para teclado (accesibilidad)

3. **Rendimiento Optimizado**
   - Carga dinámica desde API
   - Animaciones con GPU acceleration
   - Debounce en eventos de resize
   - Escape de HTML para seguridad (prevención XSS)

## Estructura de Datos del API

El menú espera datos en el siguiente formato:

```json
{
  "menu": [
    {
      "title": "Tecnología",
      "url": "/tecnologia",
      "children": [
        {
          "title": "Audio",
          "url": "/tecnologia/audio",
          "children": [
            {
              "title": "Accesorios",
              "url": "/tecnologia/audio/accesorios"
            },
            {
              "title": "Audífonos",
              "url": "/tecnologia/audio/audifonos"
            }
          ]
        }
      ]
    }
  ]
}
```

### Campos Soportados
- `title` / `name` / `label`: Título del item
- `url` / `link`: URL de destino
- `target`: Target del link (_self, _blank, etc.)
- `children`: Array de items hijos (hasta 3 niveles)

## Personalización

### Variables CSS

Puedes personalizar los colores editando las variables en `frontend.css`:

```css
:root {
    --menu-primary-color: #ff4757;        /* Color principal */
    --menu-primary-hover: #ff3838;        /* Color hover */
    --menu-text-color: #2c3e50;           /* Color de texto */
    --menu-bg-color: #ffffff;             /* Fondo del menú */
    --menu-border-color: #e1e8ed;         /* Color de bordes */
    --menu-level1-bg: #ffffff;            /* Fondo nivel 1 */
    --menu-level2-bg: #f8f9fa;            /* Fondo nivel 2 */
    --menu-level3-bg: #ecf0f1;            /* Fondo nivel 3 */
    --menu-border-radius: 8px;            /* Radio de bordes */
}
```

### Tema Oscuro

Para activar el tema oscuro, agrega la clase `dark-theme` al contenedor:

```html
<div class="menu-promesa-container dark-theme" data-menu-id="1">
```

## Configuración de Breakpoints

El menú cambia entre diseño desktop y móvil en **768px**. Puedes modificar este valor en `frontend.js`:

```javascript
var config = {
    mobileBreakpoint: 768,
    animationDuration: 300,
    hoverDelay: 200
};
```

## Animaciones Incluidas

### Desktop
- **fadeInDown**: Aparición de dropdowns con efecto de caída
- **hover**: Efecto de barra lateral en hover
- **transform**: Rotación de flechas indicadoras

### Móvil
- **slideDown**: Despliegue suave de acordeón
- **slideToggle**: Toggle suave de submenús

### General
- **fadeIn**: Aparición del menú completo
- **shimmer**: Efecto de carga tipo skeleton

## Accesibilidad

- ✅ Soporte para navegación por teclado
- ✅ Focus visible en todos los links
- ✅ ARIA labels automáticos
- ✅ Contraste adecuado de colores
- ✅ Tamaño de toque mínimo 44x44px en móvil

## Compatibilidad

### Navegadores
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Opera 76+

### Dispositivos
- ✅ Desktop (Windows, macOS, Linux)
- ✅ Tablet (iPad, Android tablets)
- ✅ Móvil (iOS, Android)

## Uso

### 1. Configurar el Endpoint
En el panel de WordPress, ve a **Menu Promesa** y configura:
- Endpoint para lista de menús: `/wp-json/custom/v1/obtenermenus`
- Endpoint del menú: `/wp-json/custom/v1/menus/:idMenu`

### 2. Agregar el Widget
1. Ve a **Apariencia > Widgets**
2. Agrega el widget "Menu Promesa" donde desees
3. Selecciona el menú del dropdown
4. Guarda los cambios

### 3. El Menú se Cargará Automáticamente
El menú cargará los datos desde el API y renderizará los 3 niveles de forma automática.

## Solución de Problemas

### El menú no aparece
- Verifica que el endpoint esté configurado correctamente
- Verifica que el API devuelva datos en el formato correcto
- Revisa la consola del navegador para errores

### Los submenús no se despliegan
- Asegúrate de que jQuery está cargado
- Verifica que no haya conflictos de JavaScript
- Comprueba que los items tengan el array `children`

### Los estilos no se aplican
- Limpia la caché del navegador
- Verifica que los archivos CSS se estén cargando
- Comprueba que no haya conflictos de CSS del tema

## Estructura de Archivos

```
menu-promesa-plugin/
├── assets/
│   ├── css/
│   │   ├── admin.css          # Estilos del admin
│   │   └── frontend.css       # Estilos del menú (MODERNIZADO)
│   └── js/
│       └── frontend.js        # JavaScript interactivo (MEJORADO)
├── admin/
│   └── class-menu-promesa-admin.php
├── includes/
│   └── class-menu-promesa-widget.php
└── menu-promesa.php
```

## Próximas Mejoras

- [ ] Megamenú con columnas
- [ ] Soporte para iconos de fuentes (FontAwesome, etc.)
- [ ] Búsqueda en el menú
- [ ] Menú sticky en scroll
- [ ] Más temas prediseñados
- [ ] Constructor visual de menús

## Créditos

- **Desarrollado por**: Steven Chilan
- **Versión**: 1.1.0
- **Licencia**: GPL v2 o superior

## Soporte

Para reportar problemas o sugerencias:
- GitHub: [https://github.com/Dario27/menu-promesa](https://github.com/Dario27/menu-promesa)
- Issues: [https://github.com/Dario27/menu-promesa/issues](https://github.com/Dario27/menu-promesa/issues)
