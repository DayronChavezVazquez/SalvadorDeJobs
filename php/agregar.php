<?php 
include 'conexion_base.php';

// Verificar si hay error de duplicados
$error_duplicados = isset($_GET['error']) && $_GET['error'] == 'duplicados';
$campos_duplicados = [];
$form_data = [];
$telefono_existente = null;
$cct_existente = null;

if ($error_duplicados && isset($_SESSION['camposDuplicados']) && isset($_SESSION['formData'])) {
    $campos_duplicados = $_SESSION['camposDuplicados'];
    $form_data = $_SESSION['formData'];
    $telefono_existente = isset($form_data['telefono_existente']) ? $form_data['telefono_existente'] : null;
    $cct_existente = isset($form_data['cct_existente']) ? $form_data['cct_existente'] : null;
    
    // Limpiar la sesión después de usar
    unset($_SESSION['camposDuplicados']);
    unset($_SESSION['formData']);
}

// Obtener el último folio y folio_interno registrados
$stmt_folio = $conn->prepare("SELECT folio, folio_interno FROM ct_departamentos ORDER BY id_departamento DESC LIMIT 1");
$stmt_folio->execute();
$ultimo_registro = $stmt_folio->fetch(PDO::FETCH_ASSOC);

// Calcular el siguiente folio y folio_interno
$siguiente_folio = 1;
$siguiente_folio_interno = 1;

if ($ultimo_registro) {
    // Si el folio es numérico, incrementarlo
    if (is_numeric($ultimo_registro['folio']) && $ultimo_registro['folio'] != '') {
        $siguiente_folio = (int)$ultimo_registro['folio'] + 1;
    } else {
        // Si no es numérico, buscar el máximo numérico usando CAST
        try {
            $stmt_max = $conn->query("SELECT MAX(CAST(folio AS UNSIGNED)) as max_folio FROM ct_departamentos WHERE folio != '' AND folio IS NOT NULL");
            $max_folio = $stmt_max->fetch(PDO::FETCH_ASSOC);
            $siguiente_folio = ($max_folio && $max_folio['max_folio'] !== null) ? (int)$max_folio['max_folio'] + 1 : 1;
        } catch (Exception $e) {
            // Si falla, usar el último ID como respaldo
            $siguiente_folio = 1;
        }
    }
    
    // Si el folio_interno es numérico, incrementarlo
    if (is_numeric($ultimo_registro['folio_interno']) && $ultimo_registro['folio_interno'] != '') {
        $siguiente_folio_interno = (int)$ultimo_registro['folio_interno'] + 1;
    } else {
        // Si no es numérico, buscar el máximo numérico usando CAST
        try {
            $stmt_max_int = $conn->query("SELECT MAX(CAST(folio_interno AS UNSIGNED)) as max_folio_int FROM ct_departamentos WHERE folio_interno != '' AND folio_interno IS NOT NULL");
            $max_folio_int = $stmt_max_int->fetch(PDO::FETCH_ASSOC);
            $siguiente_folio_interno = ($max_folio_int && $max_folio_int['max_folio_int'] !== null) ? (int)$max_folio_int['max_folio_int'] + 1 : 1;
        } catch (Exception $e) {
            // Si falla, usar el último ID como respaldo
            $siguiente_folio_interno = 1;
        }
    }
}
?>
<div class="page-header d-flex justify-content-between align-items-center mb-4">
	<h2 class="mb-0">Agregar nueva escuela</h2>
	<a class="btn btn-secondary" href="?page=consultar">← Volver</a>
</div>

<div class="card">
	<div class="card-body">
		<form action="agregar_departamento.php" method="post" class="form-grid" id="formAgregarDepartamento">
			<?php if ($error_duplicados && count($campos_duplicados) > 0): ?>
			<div id="alertaDuplicados" class="alert alert-danger alert-dismissible fade show shadow-lg" role="alert" style="border: 3px solid #dc3545; border-radius: 12px; margin-bottom: 20px;">
				<div class="d-flex align-items-start">
					<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#dc3545" class="me-3" viewBox="0 0 16 16">
						<path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
					</svg>
					<div style="flex: 1;">
						<h5 class="alert-heading mb-2" style="color: #721c24; font-weight: bold;">⚠️ Error: Datos Duplicados</h5>
						<p class="mb-2" style="color: #721c24;">Los siguientes campos ya se encuentran registrados:</p>
						<?php if (in_array('telefono', $campos_duplicados)): ?>
						<div class="mb-2 p-2" style="background-color: #f8d7da; border: 2px solid #dc3545; border-radius: 6px;">
							<strong>📞 Teléfono:</strong> <?= htmlspecialchars($form_data['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?> ya está registrado en: <strong><?= htmlspecialchars($telefono_existente ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
						</div>
						<?php endif; ?>
						<?php if (in_array('cct', $campos_duplicados)): ?>
						<div class="mb-2 p-2" style="background-color: #f8d7da; border: 2px solid #dc3545; border-radius: 6px;">
							<strong>🏫 CCT:</strong> <?= htmlspecialchars($form_data['cct'] ?? '', ENT_QUOTES, 'UTF-8') ?> ya está registrado en: <strong><?= htmlspecialchars($cct_existente ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
						</div>
						<?php endif; ?>
					</div>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			</div>
			<?php endif; ?>
			<div class="mb-3">
				<label class="form-label">Nombre escuela <span class="text-danger">*</span></label>
				<input type="text" name="nombre_departamento" class="form-control" placeholder="Nombre escuela" value="<?= htmlspecialchars($form_data['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Teléfono <span class="text-danger">*</span></label>
				<input type="text" name="telefono" id="telefono" class="form-control" placeholder="Teléfono" maxlength="10" value="<?= htmlspecialchars($form_data['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required <?= in_array('telefono', $campos_duplicados) ? 'style="border: 3px solid #dc3545; box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);"' : '' ?>>
				<small class="text-muted">Máximo 10 dígitos</small>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Folio</label>
					<input type="text" name="folio" id="folio" class="form-control" value="<?= $siguiente_folio ?>" readonly disabled style="background-color: #e9ecef;">
					<small class="text-muted">Generado automáticamente</small>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Folio interno</label>
					<input type="text" name="folio_interno" id="folio_interno" class="form-control" value="<?= $siguiente_folio_interno ?>" readonly disabled style="background-color: #e9ecef;">
					<small class="text-muted">Generado automáticamente</small>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Encargado <span class="text-danger">*</span></label>
					<input type="text" name="nombre_encargado" class="form-control" placeholder="Nombre del encargado" value="<?= htmlspecialchars($form_data['nombre_encargado'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Cargo <span class="text-danger">*</span></label>
					<input type="text" name="cargo" class="form-control" placeholder="Cargo" value="<?= htmlspecialchars($form_data['cargo'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label">Domicilio <span class="text-danger">*</span></label>
				<input type="text" name="domicilio" class="form-control" placeholder="Domicilio" value="<?= htmlspecialchars($form_data['domicilio'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
			</div>
			<div class="mb-3">
				<label class="form-label">CCT <span class="text-danger">*</span></label>
				<input type="text" name="cct" id="cct" class="form-control" placeholder="CCT" value="<?= htmlspecialchars($form_data['cct'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required <?= in_array('cct', $campos_duplicados) ? 'style="border: 3px solid #dc3545; box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);"' : '' ?>>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">Guardar</button>
				<a class="btn btn-outline-secondary" href="?page=consultar">Cancelar</a>
			</div>
		</form>
	</div>
</div>

<script>
$(document).ready(function() {
	// Convertir CCT a mayúsculas automáticamente
	$('#cct').on('input', function() {
		$(this).val($(this).val().toUpperCase());
	});
	
	// Limitar teléfono a solo números y máximo 10 dígitos
	$('#telefono').on('input', function() {
		var valor = $(this).val().replace(/\D/g, ''); // Solo números
		if (valor.length > 10) {
			valor = valor.substring(0, 10);
		}
		$(this).val(valor);
	});
	
	// Si hay alerta de duplicados, cerrarla automáticamente después de 5 segundos
	<?php if ($error_duplicados && count($campos_duplicados) > 0): ?>
	setTimeout(function() {
		$('#alertaDuplicados').fadeOut(300, function() {
			$(this).remove();
		});
	}, 5000);
	
	// Remover el borde rojo cuando el usuario empiece a escribir
	$('input[name="telefono"], input[name="cct"]').on('input', function() {
		$(this).css({
			'border': '',
			'box-shadow': ''
		});
	});
	
	// Enfocar el primer campo con error
	<?php if (in_array('telefono', $campos_duplicados)): ?>
	setTimeout(function() {
		$('#telefono').focus();
	}, 100);
	<?php elseif (in_array('cct', $campos_duplicados)): ?>
	setTimeout(function() {
		$('#cct').focus();
	}, 100);
	<?php endif; ?>
	<?php endif; ?>
	
	// Habilitar los campos disabled antes de enviar el formulario
	$('#formAgregarDepartamento').on('submit', function() {
		$('#folio').prop('disabled', false);
		$('#folio_interno').prop('disabled', false);
	});
});
</script>


