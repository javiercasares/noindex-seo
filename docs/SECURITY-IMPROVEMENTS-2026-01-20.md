# Mejoras de Seguridad Recomendadas - noindex SEO v2.0.0
**Fecha**: 2026-01-20
**Versión**: 2.0.0 Post-Audit
**Basado en**: SECURITY-AUDIT-2026-01-20.md

## Resumen

Este documento detalla las implementaciones sugeridas para las vulnerabilidades menores identificadas en la auditoría. Todas son de severidad **BAJA** o **MEDIA** y NO representan riesgo inmediato de explotación.

**Estado actual del plugin**: ✅ APROBADO PARA PRODUCCIÓN

---

## 1. MEDIA - Usar prepare() en uninstall.php

### Severidad
🟡 **MEDIA** (Mejora de calidad de código)

### Ubicación
`uninstall.php:80-89`

### Problema Actual
```php
// Líneas 80-89
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_noindex_seo_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_noindex_seo_%'");
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_noindex_seo_override'");
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_noindex_seo_noindex'");
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_noindex_seo_nofollow'");
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_noindex_seo_noarchive'");
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_noindex_seo_nosnippet'");
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_noindex_seo_noimageindex'");
```

### Riesgo Real
- **MUY BAJO**: Strings son literales hardcodeadas
- Solo ejecutable por administradores
- Solo durante desinstalación del plugin

### Implementación Sugerida

```php
// Clean up transients.
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		'_transient_noindex_seo_%'
	)
);
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		'_transient_timeout_noindex_seo_%'
	)
);

// Clean up post meta (granular control).
$meta_keys = array(
	'_noindex_seo_override',
	'_noindex_seo_noindex',
	'_noindex_seo_nofollow',
	'_noindex_seo_noarchive',
	'_noindex_seo_nosnippet',
	'_noindex_seo_noimageindex',
);

foreach ( $meta_keys as $meta_key ) {
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
			$meta_key
		)
	);
}
```

### Beneficios
- Consistencia con el resto del código
- Mejores prácticas de WordPress
- Protección adicional contra futuros cambios

### Esfuerzo
⏱️ **15 minutos**

### Prioridad
🟡 **MEDIA** - Implementar en v2.0.1 o v2.1.0

---

## 2. BAJA - Validar Filter Contra Whitelist

### Severidad
🟢 **BAJA** (Defense in depth)

### Ubicación
`noindex-seo.php:1635`

### Problema Actual
```php
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- URL parameter for filtering, not form data.
$filter = sanitize_text_field( wp_unslash( $_GET['noindex_seo_filter'] ) );

// Build meta query.
$meta_query = array();
```

El filtro sanitiza pero no valida contra valores permitidos.

### Riesgo Real
- **MUY BAJO**: No hay SQL injection (WordPress maneja meta_query)
- Valor inválido simplemente no tendría efecto
- Ya está sanitizado con `sanitize_text_field()`

### Implementación Sugerida

```php
// Check if filter is set (URL parameter, not form submission, no nonce needed).
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- URL parameter for filtering, not form data.
if ( ! isset( $_GET['noindex_seo_filter'] ) || empty( $_GET['noindex_seo_filter'] ) ) {
	return;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- URL parameter for filtering, not form data.
$filter = sanitize_text_field( wp_unslash( $_GET['noindex_seo_filter'] ) );

// Validate against whitelist.
$valid_filters = array( 'with_override', 'without_override' );
if ( ! in_array( $filter, $valid_filters, true ) ) {
	return; // Invalid filter value, ignore.
}

// Build meta query.
$meta_query = array();
```

### Beneficios
- Previene valores arbitrarios (aunque ya sanitizados)
- Mejora claridad del código
- Defense in depth

### Esfuerzo
⏱️ **5 minutos**

### Prioridad
🟢 **BAJA** - Implementar en v2.1.0

---

## 3. INFORMATIVO - Validar Contexts en Form Processing

### Severidad
ℹ️ **INFORMATIVA** (Mejora de consistencia)

### Ubicación
`noindex-seo.php:778-795`

### Contexto
Actualmente el código itera sobre un array de contexts hardcodeado pero no valida que los valores de POST correspondan a contexts válidos.

### Implementación Sugerida

```php
// Define valid contexts.
$contexts = array(
	'error',
	'archive',
	'attachment',
	// ... resto de contexts
);

$directives = array( 'noindex', 'nofollow', 'noarchive', 'nosnippet', 'noimageindex' );

// Reset all options to 0 and process form data.
foreach ( $contexts as $context ) {
	// Validate context is in our allowed list (defense in depth).
	if ( ! in_array( $context, $contexts, true ) ) {
		continue; // Skip invalid context.
	}

	foreach ( $directives as $directive ) {
		$option_key   = $directive . '_seo_' . $context;
		$option_value = isset( $_POST[ $option_key ] )
			? sanitize_text_field( wp_unslash( $_POST[ $option_key ] ) )
			: '';

		// Additional validation: only accept '1' or empty.
		if ( '' !== $option_value && '1' !== $option_value ) {
			$option_value = ''; // Invalid value, treat as unchecked.
		}

		// ... resto del código
	}
}
```

### Beneficios
- Consistencia
- Protección contra modificaciones futuras
- Claridad del código

### Esfuerzo
⏱️ **10 minutos**

### Prioridad
ℹ️ **INFORMATIVA** - Opcional para v2.1.0

---

## 4. Mejoras Futuras (Opcional - No Urgente)

### 4.1. Rate Limiting en Bulk Actions

**Objetivo**: Prevenir abuso de operaciones masivas

**Implementación**:
```php
// Add transient-based rate limiting.
$user_id = get_current_user_id();
$rate_limit_key = 'noindex_seo_bulk_action_' . $user_id;

if ( get_transient( $rate_limit_key ) ) {
	// User already performed bulk action recently.
	$redirect_to = add_query_arg(
		'noindex_seo_rate_limit',
		'1',
		$redirect_to
	);
	return $redirect_to;
}

// Set rate limit: 1 bulk action per minute.
set_transient( $rate_limit_key, 1, MINUTE_IN_SECONDS );
```

**Consideración**: v2.1.0 o posterior

### 4.2. Audit Log de Cambios

**Objetivo**: Registrar cambios en directivas para auditoría

**Implementación**:
```php
// Log directive changes.
function noindex_seo_log_change( $post_id, $directive, $old_value, $new_value ) {
	if ( ! $granular_enabled = get_option( 'noindex_seo_config_log', 0 ) ) {
		return;
	}

	$log_entry = array(
		'timestamp' => current_time( 'mysql' ),
		'user_id'   => get_current_user_id(),
		'post_id'   => $post_id,
		'directive' => $directive,
		'old_value' => $old_value,
		'new_value' => $new_value,
	);

	// Store in custom table or use existing logging system.
	// Implementation depends on requirements.
}
```

**Consideración**: v2.2.0 o posterior (requiere nueva feature)

### 4.3. Two-Factor para Settings Page

**Objetivo**: Capa extra de seguridad para cambios críticos

**Implementación**:
Usar plugin externo como "Two Factor Authentication" o similar.

**Consideración**: Opcional, solo para instalaciones críticas

---

## Priorización de Implementación

### Para v2.0.1 (Patch Release)
- ✅ Implementar mejora #1 (prepare() en uninstall)

### Para v2.1.0 (Minor Release)
- ✅ Implementar mejora #2 (validar filter)
- ⚠️ Considerar mejora #3 (validar contexts)

### Para v2.2.0+ (Feature Release)
- ⚠️ Evaluar rate limiting (#4.1)
- ⚠️ Evaluar audit log (#4.2)

---

## Testing Recomendado Post-Implementación

### Test Plan para Mejoras #1 y #2

```
1. Test de Uninstall:
   [ ] Instalar plugin
   [ ] Configurar varias directivas
   [ ] Activar granular control
   [ ] Crear posts con overrides
   [ ] Desinstalar plugin desde admin
   [ ] Verificar limpieza completa en DB

2. Test de Filter:
   [ ] Ir a lista de posts
   [ ] Aplicar filtro "With override"
   [ ] Verificar resultados correctos
   [ ] Aplicar filtro "Without override"
   [ ] Verificar resultados correctos
   [ ] Intentar URL con valor inválido:
       ?noindex_seo_filter=invalid_value
   [ ] Verificar que se ignora (no error)

3. Regression Testing:
   [ ] Todas las funcionalidades existentes funcionan
   [ ] No hay errores en error_log
   [ ] PHPCS pasa sin warnings
```

---

## Conclusión

Las mejoras recomendadas son de **severidad baja** y pueden implementarse de forma gradual. El plugin está **100% funcional y seguro** en su estado actual.

**Recomendación**: Implementar mejora #1 en próximo patch release por consistencia de código. El resto son opcionales y pueden esperar a futuras versiones.

---

**Aprobado por**: Auditoría de Seguridad 2026-01-20
**Revisión siguiente**: Al release de v2.1.0
