# Parches de Seguridad Recomendados
**Fecha:** 2026-01-20
**Versión del Plugin:** 1.2.0
**Relacionado con:** SECURITY-2026-01-20.md

Este documento contiene los parches de código específicos para corregir las vulnerabilidades identificadas en la auditoría de seguridad.

---

## Parche 1: Agregar Verificación de Capabilities en noindex_seo_admin()

**Prioridad:** 🔴 ALTA
**Archivo:** `noindex-seo.php`
**Línea:** 454
**Función:** `noindex_seo_admin()`

### Código Actual
```php
function noindex_seo_admin() {
	// Define sections and their respective settings.
	$sections = array(
		'main_pages'  => array(
```

### Código Corregido
```php
function noindex_seo_admin() {
	// Verify user capabilities for defense in depth.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die(
			esc_html__( 'You do not have sufficient permissions to access this page.', 'noindex-seo' ),
			esc_html__( 'Permission Denied', 'noindex-seo' ),
			array( 'response' => 403 )
		);
	}

	// Define sections and their respective settings.
	$sections = array(
		'main_pages'  => array(
```

### Explicación
Agrega una verificación explícita de capabilities al inicio de la función para prevenir acceso no autorizado. El parámetro adicional en `wp_die()` proporciona un código de respuesta HTTP apropiado (403 Forbidden).

---

## Parche 2: Mejorar Sanitización de Inputs en noindex_seo_process_form()

**Prioridad:** 🟡 MEDIA
**Archivo:** `noindex-seo.php`
**Líneas:** 416-427
**Función:** `noindex_seo_process_form()`

### Código Actual (Líneas 416-421)
```php
// Save only active options (checked checkboxes).
foreach ( $settings as $setting ) {
	if ( isset( $_POST[ 'noindex_seo_' . $setting ] ) ) {
		update_option( 'noindex_seo_' . $setting, 1 );
	}
}
```

### Código Corregido
```php
// Save only active options (checked checkboxes).
foreach ( $settings as $setting ) {
	$option_key   = 'noindex_seo_' . $setting;
	$option_value = isset( $_POST[ $option_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $option_key ] ) ) : '';

	// Only set to 1 if the checkbox was actually checked (value should be "1").
	if ( '1' === $option_value ) {
		update_option( $option_key, 1 );
	}
}
```

### Código Actual (Líneas 423-427)
```php
// Save general configuration option.
update_option(
	'noindex_seo_config_seoplugins',
	isset( $_POST['noindex_seo_config_seoplugins'] ) ? 1 : 0
);
```

### Código Corregido
```php
// Save general configuration option.
$config_value = isset( $_POST['noindex_seo_config_seoplugins'] )
	? absint( $_POST['noindex_seo_config_seoplugins'] )
	: 0;

// Ensure value is either 0 or 1.
$config_value = ( 1 === $config_value ) ? 1 : 0;

update_option( 'noindex_seo_config_seoplugins', $config_value );
```

### Explicación
- Agrega sanitización explícita con `sanitize_text_field()` y `wp_unslash()`
- Valida que el valor sea exactamente "1" antes de guardarlo
- Para la configuración de plugins, usa `absint()` y valida que sea 0 o 1

---

## Parche 3: Escapar Atributos HTML Dinámicos

**Prioridad:** 🟡 MEDIA
**Archivo:** `noindex-seo.php`
**Línea:** 686
**Función:** `noindex_seo_admin()`

### Código Actual
```php
echo '<input type="checkbox" id="noindex_seo_' . esc_attr( $field_id ) . '" name="noindex_seo_' . esc_attr( $field_id ) . '" value="1" ' . checked( 1, $option, false ) . '> ';
echo esc_html( $field['recommended'] ) . ': <span class="dashicons ' . ( $field['suggestion'] ? 'dashicons-yes' : 'dashicons-no' ) . '" title="' . ( $field['suggestion'] ? 'Yes' : 'No' ) . '"></span>. ';

echo '<span class="description">' . esc_html( $field['description'] ) . '</span>';
```

### Código Corregido
```php
echo '<input type="checkbox" id="noindex_seo_' . esc_attr( $field_id ) . '" name="noindex_seo_' . esc_attr( $field_id ) . '" value="1" ' . checked( 1, $option, false ) . '> ';

// Prepare dashicon attributes.
$dashicon_class = $field['suggestion'] ? 'dashicons-yes' : 'dashicons-no';
$dashicon_title = $field['suggestion'] ? esc_attr__( 'Yes', 'noindex-seo' ) : esc_attr__( 'No', 'noindex-seo' );

echo esc_html( $field['recommended'] ) . ': <span class="dashicons ' . esc_attr( $dashicon_class ) . '" title="' . $dashicon_title . '"></span>. ';

echo '<span class="description">' . esc_html( $field['description'] ) . '</span>';
```

### Explicación
- Separa la lógica de selección de valores en variables
- Usa `esc_attr()` para escapar atributos HTML
- Usa `esc_attr__()` para strings traducibles en atributos
- Hace el código más legible y seguro

---

## Parche 4: Validar Parámetros GET en Admin (Opcional pero Recomendado)

**Prioridad:** 🟢 BAJA
**Archivo:** `noindex-seo.php`
**Línea:** 454 (inicio de función)
**Función:** `noindex_seo_admin()`

### Código a Agregar (después de la verificación de capabilities)
```php
function noindex_seo_admin() {
	// Verify user capabilities for defense in depth.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die(
			esc_html__( 'You do not have sufficient permissions to access this page.', 'noindex-seo' ),
			esc_html__( 'Permission Denied', 'noindex-seo' ),
			array( 'response' => 403 )
		);
	}

	// Check for success message.
	$show_success = false;
	if ( isset( $_GET['updated'] ) && 'true' === $_GET['updated'] ) {
		// Sanitize GET parameter.
		$updated_param = sanitize_text_field( wp_unslash( $_GET['updated'] ) );
		$show_success  = ( 'true' === $updated_param );
	}

	// Define sections and their respective settings.
	$sections = array(
		// ...
	);

	?>
	<div class="wrap">
		<h1><?php echo esc_html( __( 'noindex SEO Settings', 'noindex-seo' ) ); ?></h1>

		<?php if ( $show_success ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo esc_html__( 'Settings saved successfully.', 'noindex-seo' ); ?></p>
			</div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
```

### Explicación
- Valida y sanitiza el parámetro GET `updated`
- Agrega un mensaje de éxito cuando se guardan las configuraciones
- Mejora la experiencia de usuario mientras mantiene la seguridad

---

## Parche 5: Mejorar Seguridad del Hook de Clear Transient

**Prioridad:** 🟢 BAJA
**Archivo:** `noindex-seo.php`
**Línea:** 282
**Función:** `noindex_seo_register()`

### Código Actual
```php
// Hook to settings update to clear transient cache.
add_action( 'update_option_noindexseo', 'noindex_seo_clear_transient', 10, 2 );
```

### Código Corregido
```php
// Hook to settings update to clear transient cache.
// Note: Hook receives $old_value and $value parameters but we don't need them.
add_action( 'update_option_noindexseo', 'noindex_seo_clear_transient', 10, 0 );
```

### Y actualizar la función noindex_seo_clear_transient()
```php
/**
 * Clears the cached plugin settings stored in the transient.
 *
 * This function deletes the 'noindex_seo_options' transient to ensure that updated
 * option values are fetched fresh from the database on the next request. It is typically
 * triggered after the plugin settings are updated to prevent stale data from being used.
 *
 * Hooked to the {@see 'update_option_noindexseo'} action.
 *
 * @since 1.0.0
 *
 * @return void
 */
function noindex_seo_clear_transient() {
	// Verify we're in a valid admin context.
	if ( ! is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	// Delete the transient cache.
	$deleted = delete_transient( 'noindex_seo_options' );

	// Log for debugging purposes (optional, remove in production).
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( sprintf(
			'noindex SEO: Transient cache %s at %s',
			$deleted ? 'cleared successfully' : 'clear failed',
			current_time( 'mysql' )
		) );
	}
}
```

### Explicación
- Cambia el número de parámetros aceptados de 2 a 0 ya que no se usan
- Agrega verificación de contexto admin
- Agrega logging opcional para debugging

---

## Parche 6: Fortalecer Validación en noindex_seo_show()

**Prioridad:** 🟢 BAJA
**Archivo:** `noindex-seo.php`
**Línea:** 78-119
**Función:** `noindex_seo_show()`

### Código a Agregar (después de apply_filters)
```php
/**
 * Filter the contexts and corresponding option keys used for noindex.
 *
 * @since 1.0.0.
 *
 * @param array $contexts Associative array of context => option_key.
 */
$contexts = apply_filters(
	'noindex_seo_contexts',
	array(
		'single'            => 'noindex_seo_single',
		'page'              => 'noindex_seo_page',
		// ... resto del array
	)
);

// Validate filtered contexts to prevent injection of invalid option names.
if ( is_array( $contexts ) ) {
	foreach ( $contexts as $context => $option_key ) {
		// Ensure option_key follows expected pattern.
		if ( ! is_string( $option_key ) || 0 !== strpos( $option_key, 'noindex_seo_' ) ) {
			unset( $contexts[ $context ] );
		}
	}
} else {
	// If contexts is not an array after filtering, reset to default.
	$contexts = array();
}
```

### Explicación
- Valida que los contextos filtrados sean seguros
- Previene inyección de nombres de opciones arbitrarias
- Protege contra plugins maliciosos que usen el filtro

---

## Parche 7: Agregar Verificación de Integridad (Opcional)

**Prioridad:** 🟢 OPCIONAL
**Archivo:** Nuevo archivo `noindex-seo-integrity.php`

### Crear nuevo archivo para verificación de integridad
```php
<?php
/**
 * Security Integrity Check for noindex SEO Plugin
 *
 * @package noindex-seo
 * @since 1.3.0
 */

defined( 'ABSPATH' ) || die( 'Bye bye!' );

/**
 * Verifies plugin file integrity.
 *
 * @since 1.3.0
 * @return bool True if integrity check passes, false otherwise.
 */
function noindex_seo_verify_integrity() {
	// Only check in admin area.
	if ( ! is_admin() ) {
		return true;
	}

	// Define expected file hashes (update these when releasing new versions).
	$expected_hashes = array(
		'noindex-seo.php' => '', // Update with actual hash
		'uninstall.php'   => '', // Update with actual hash
	);

	$plugin_dir = plugin_dir_path( __FILE__ );
	$failed     = false;

	foreach ( $expected_hashes as $file => $expected_hash ) {
		$file_path = $plugin_dir . $file;

		if ( ! file_exists( $file_path ) ) {
			$failed = true;
			continue;
		}

		$actual_hash = hash_file( 'sha256', $file_path );

		if ( $expected_hash !== $actual_hash && ! empty( $expected_hash ) ) {
			$failed = true;

			// Log the integrity failure.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( sprintf(
					'noindex SEO: Integrity check failed for %s. Expected: %s, Got: %s',
					$file,
					$expected_hash,
					$actual_hash
				) );
			}
		}
	}

	if ( $failed ) {
		add_action(
			'admin_notices',
			function() {
				?>
				<div class="notice notice-error">
					<p>
						<strong><?php echo esc_html__( 'Security Warning:', 'noindex-seo' ); ?></strong>
						<?php echo esc_html__( 'noindex SEO plugin file integrity check failed. The plugin files may have been modified. Please reinstall the plugin from a trusted source.', 'noindex-seo' ); ?>
					</p>
				</div>
				<?php
			}
		);
	}

	return ! $failed;
}

// Run integrity check on admin_init.
add_action( 'admin_init', 'noindex_seo_verify_integrity' );
```

### Para incluir en noindex-seo.php
```php
// Include integrity check (if file exists).
if ( file_exists( plugin_dir_path( __FILE__ ) . 'noindex-seo-integrity.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'noindex-seo-integrity.php';
}
```

### Explicación
- Verifica la integridad de los archivos principales del plugin
- Alerta al administrador si los archivos han sido modificados
- Ayuda a detectar modificaciones maliciosas

---

## Implementación de Parches

### Orden Recomendado de Implementación

1. **Parche 1** (Alta Prioridad) - Verificación de capabilities
2. **Parche 2** (Media Prioridad) - Sanitización de inputs
3. **Parche 3** (Media Prioridad) - Escapado de atributos HTML
4. **Parche 4** (Baja Prioridad) - Validación de parámetros GET
5. **Parche 5** (Baja Prioridad) - Mejora de hook de transient
6. **Parche 6** (Baja Prioridad) - Validación de contextos filtrados
7. **Parche 7** (Opcional) - Verificación de integridad

### Testing Después de Aplicar Parches

```bash
# 1. Ejecutar PHP_CodeSniffer
vendor/bin/phpcs --standard=WordPress noindex-seo.php

# 2. Verificar compatibilidad PHP
vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 5.6- noindex-seo.php

# 3. Probar funcionalidad
# - Acceder a la página de settings
# - Guardar configuraciones
# - Verificar que las opciones se aplican correctamente
# - Probar con usuario sin privilegios

# 4. Verificar que no hay regresiones
# - Probar todas las opciones de noindex
# - Verificar detección de conflictos
# - Probar desinstalación
```

### Checklist de Validación

- [ ] Parche 1 aplicado y probado
- [ ] Parche 2 aplicado y probado
- [ ] Parche 3 aplicado y probado
- [ ] Todas las funcionalidades existentes funcionan
- [ ] No hay errores PHP warnings/notices
- [ ] PHP_CodeSniffer pasa sin errores
- [ ] Compatibilidad PHP 5.6-8.4 verificada
- [ ] Probado en WordPress 4.1 - 6.8
- [ ] Tests manuales de seguridad pasados
- [ ] Documentación actualizada

---

## Notas de Versión Sugeridas

```markdown
### Version 1.2.1 - Security Hardening Release

**Security Improvements:**
- Added explicit capability checks in admin functions for defense in depth
- Enhanced input sanitization in form processing
- Improved HTML attribute escaping throughout admin interface
- Added validation for filtered contexts to prevent option key injection
- Strengthened transient cache clearing with context verification

**Technical Changes:**
- No functional changes for end users
- All existing features work exactly as before
- Improved code security following WordPress best practices

**Upgrade Notice:**
This is a security hardening release. While no critical vulnerabilities were found,
these improvements add additional layers of protection. Upgrade recommended for all users.
```

---

## Comandos Útiles para Desarrollo

```bash
# Generar hashes para verificación de integridad
sha256sum noindex-seo.php
sha256sum uninstall.php

# Buscar potenciales problemas de seguridad
grep -r "eval\|exec\|system\|passthru\|shell_exec" .
grep -r "\$_GET\|\$_POST\|\$_REQUEST" . | grep -v "esc_\|sanitize_"

# Verificar permisos de archivos
find . -type f -name "*.php" ! -perm 644

# Auditar dependencias de Composer
composer audit

# Buscar strings no escapados
grep -r "echo.*\$" . | grep -v "esc_"
```

---

## Contacto y Soporte

Si tienes dudas sobre la implementación de estos parches:
1. Revisa la auditoría completa en `SECURITY-2026-01-20.md`
2. Consulta la documentación de WordPress Security: https://developer.wordpress.org/plugins/security/
3. Contacta al equipo de desarrollo del plugin

---

**Fecha de Creación:** 2026-01-20
**Última Actualización:** 2026-01-20
**Revisión:** 1.0
