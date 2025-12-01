# Sidebar Lateral Automático - Menu Promesa

## Descripción

El plugin Menu Promesa incluye un **sistema de sidebar lateral fijo** que se puede activar para mostrar automáticamente un menú en todas las páginas de tu sitio WordPress.

## Características Principales

### 🎨 Diseño Moderno
- **Sidebar fijo** en el lado izquierdo o derecho de la pantalla
- **Diseño moderno** con gradientes y sombras suaves
- **Scrollbar personalizado** con los colores del tema
- **Animaciones fluidas** al abrir/cerrar

### 📱 100% Responsive
- **Desktop**: Sidebar siempre visible, el contenido se ajusta automáticamente
- **Móvil**: Sidebar oculto con botón hamburguesa para abrir/cerrar
- **Overlay oscuro** en móvil para mejor UX

### ⚙️ Totalmente Configurable
- **Activar/Desactivar** el sidebar desde el panel admin
- **Seleccionar qué menú** mostrar
- **Elegir posición**: Izquierda o derecha
- **Sin código**: Todo se configura desde WordPress

## Configuración

### 1. Acceder al Panel de Configuración

1. En el admin de WordPress, ve a **Menu Promesa**
2. Encontrarás una sección llamada **"Configuración de Sidebar Automático"**

### 2. Activar el Sidebar

- Marca la casilla **"Activar sidebar automático"**
- Esto hará que el menú aparezca automáticamente en todas las páginas

### 3. Seleccionar el Menú

- En el dropdown **"Menú a mostrar en sidebar"**, selecciona el menú que deseas mostrar
- Los menús disponibles se cargan automáticamente desde tu API

### 4. Elegir la Posición

- **Izquierda**: El sidebar aparecerá en el lado izquierdo de la pantalla
- **Derecha**: El sidebar aparecerá en el lado derecho de la pantalla

### 5. Guardar Cambios

- Haz clic en **"Guardar Configuración"**
- El sidebar aparecerá inmediatamente en tu sitio

## Comportamiento

### En Desktop (> 768px)

- El sidebar está **siempre visible** ocupando 280px de ancho
- El contenido del sitio se ajusta automáticamente
  - Sidebar izquierdo: El contenido se mueve 280px a la derecha
  - Sidebar derecho: El contenido se mueve 280px a la izquierda
- Menú interactivo tipo acordeón
- Submenús se expanden/contraen al hacer clic

### En Móvil (≤ 768px)

- El sidebar está **oculto por defecto**
- Se muestra un **botón hamburguesa** flotante
- Al hacer clic en el botón:
  - El sidebar se desliza desde el lado configurado
  - Aparece un overlay oscuro sobre el contenido
  - El scroll del body se bloquea
- Para cerrar el sidebar:
  - Botón X en el header del sidebar
  - Clic en el overlay
  - Presionar tecla ESC
  - Clic en cualquier link del menú

## Estructura del Sidebar

```
┌─────────────────────────────┐
│  MENÚ                    ×  │ ← Header (rojo/gradiente)
├─────────────────────────────┤
│  📁 Tecnología          ⏷   │ ← Nivel 1
│    📁 Audio             ⏷   │ ← Nivel 2 (expandido)
│      📄 Accesorios          │ ← Nivel 3
│      📄 Audífonos           │
│      📄 Barras de Sonido    │
│    📁 TV y Video        ⏵   │ ← Nivel 2 (contraído)
│                             │
│  📁 Hogar               ⏵   │ ← Nivel 1
│                             │
│  ...                        │
│                             │
└─────────────────────────────┘
```

## Personalización CSS

### Variables CSS Disponibles

Puedes personalizar los colores editando `/assets/css/sidebar.css`:

```css
:root {
    --sidebar-width: 280px;              /* Ancho del sidebar */
    --sidebar-bg: #ffffff;               /* Fondo del sidebar */
    --sidebar-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);  /* Sombra */
    --sidebar-header-bg: #ff4757;        /* Color del header */
    --sidebar-header-text: #ffffff;      /* Texto del header */
    --sidebar-z-index: 9999;             /* Z-index */
}
```

### Cambiar el Ancho del Sidebar

Para cambiar el ancho del sidebar:

```css
:root {
    --sidebar-width: 320px; /* Nuevo ancho */
}
```

**Nota**: El contenido se ajustará automáticamente al nuevo ancho.

### Cambiar Colores del Header

```css
.menu-promesa-sidebar-header {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}
```

## Funcionalidades Avanzadas

### 1. Detección de Página Activa

- El sidebar detecta automáticamente la página actual
- Marca el item correspondiente como activo
- Expande automáticamente los padres del item activo

### 2. Scrollbar Personalizado

- Scrollbar delgado y moderno (6px)
- Color acorde al tema del plugin
- Hover effect en el thumb

### 3. Accesibilidad

- ✅ Navegación por teclado completa
- ✅ Cierre con tecla ESC
- ✅ ARIA labels en botones
- ✅ Focus visible
- ✅ Soporte para `prefers-reduced-motion`

### 4. Prevención de Scroll Doble

- En móvil, cuando el sidebar está abierto:
  - El scroll del body se bloquea
  - Solo el sidebar hace scroll
  - Esto evita que se desplace el contenido de fondo

## Compatibilidad con Temas

El sidebar funciona con la mayoría de los temas de WordPress porque:

- Se inyecta automáticamente en el `wp_footer`
- Usa `position: fixed` para no interferir con el layout
- Ajusta el `margin` del body solo en desktop
- No requiere modificaciones en el tema

### Posibles Conflictos

Si tu tema ya tiene un sidebar fijo o un menú con alto z-index:

1. **Cambiar z-index del sidebar**:
```css
:root {
    --sidebar-z-index: 99999; /* Aumentar si es necesario */
}
```

2. **Desactivar temporalmente**:
   - Ve a Menu Promesa > Desmarca "Activar sidebar automático"

## Desactivar el Sidebar

Para desactivar el sidebar sin desinstalar el plugin:

1. Ve a **Menu Promesa** en el admin
2. Desmarca **"Activar sidebar automático"**
3. Guarda cambios
4. El sidebar dejará de aparecer inmediatamente

## Archivos del Sistema

```
menu-promesa-plugin/
├── includes/
│   └── class-menu-promesa-sidebar.php    # Lógica del sidebar
├── admin/
│   └── class-menu-promesa-admin.php      # Configuración en admin
├── assets/
│   ├── css/
│   │   └── sidebar.css                   # Estilos del sidebar
│   └── js/
│       └── sidebar.js                    # JavaScript del sidebar
└── menu-promesa.php                       # Plugin principal
```

## Ejemplo de Uso

### Caso 1: E-commerce con Categorías

```
Configuración:
✓ Activar sidebar automático
✓ Menú: "Categorías de Productos"
✓ Posición: Izquierda

Resultado:
- Sidebar con todas las categorías siempre visible
- Navegación rápida entre secciones
- 3 niveles: Categoría > Subcategoría > Producto
```

### Caso 2: Blog con Secciones

```
Configuración:
✓ Activar sidebar automático
✓ Menú: "Secciones del Blog"
✓ Posición: Derecha

Resultado:
- Sidebar en el lado derecho
- Menú con secciones y artículos destacados
- Fácil navegación entre posts
```

### Caso 3: Portal de Noticias

```
Configuración:
✓ Activar sidebar automático
✓ Menú: "Categorías de Noticias"
✓ Posición: Izquierda

Resultado:
- Sidebar con categorías y subcategorías
- Navegación tipo portal profesional
- Menú siempre accesible
```

## Solución de Problemas

### El sidebar no aparece

1. **Verifica que esté activado**:
   - Ve a Menu Promesa
   - Asegúrate de que "Activar sidebar automático" esté marcado

2. **Verifica que haya un menú seleccionado**:
   - Debe haber un menú seleccionado en "Menú a mostrar en sidebar"

3. **Limpia la caché**:
   - Limpia la caché del navegador
   - Si usas un plugin de caché, límpialo también

### El sidebar cubre el contenido

- Esto solo debería pasar si hay un conflicto de CSS
- Solución: Aumenta el z-index del contenido de tu tema
- O reduce el z-index del sidebar en `sidebar.css`

### El botón hamburguesa no funciona

1. **Verifica que jQuery esté cargado**:
   - Abre la consola del navegador (F12)
   - Busca errores de JavaScript

2. **Conflictos con otros plugins**:
   - Desactiva otros plugins temporalmente
   - Reactiva uno por uno para identificar el conflicto

### El menú no se carga

1. **Verifica el endpoint**:
   - Ve a Menu Promesa > Configuración de API
   - Asegúrate de que los endpoints estén correctos

2. **Prueba el endpoint manualmente**:
   - Copia la URL del endpoint
   - Pégala en el navegador
   - Deberías ver los datos JSON del menú

## Actualizaciones Futuras

- [ ] Opción de ancho personalizable desde admin
- [ ] Temas de color predefinidos
- [ ] Modo oscuro/claro con toggle
- [ ] Iconos personalizados para items
- [ ] Búsqueda dentro del sidebar
- [ ] Múltiples menús con tabs
- [ ] Minificar sidebar (modo compacto)

## Soporte

Si encuentras algún problema o tienes sugerencias:

- **GitHub Issues**: [https://github.com/Dario27/menu-promesa/issues](https://github.com/Dario27/menu-promesa/issues)
- **Documentación completa**: Ver `MENU-MULTINIVEL.md`

---

**Versión**: 1.2.0
**Autor**: Steven Chilan
**Licencia**: GPL v2 o superior
