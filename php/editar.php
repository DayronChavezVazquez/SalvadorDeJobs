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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare('SELECT * FROM ct_departamentos WHERE id_departamento = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$dep = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$dep) { echo '<p>No encontrado</p>'; return; }

// Si hay datos del formulario en sesión, usarlos; si no, usar los datos de la BD
$nombre_departamento = $error_duplicados && isset($form_data['nombre']) ? $form_data['nombre'] : ($dep['nombre_departamento'] ?? '');
$telefono = $error_duplicados && isset($form_data['telefono']) ? $form_data['telefono'] : ($dep['telefono'] ?? '');
$folio = $error_duplicados && isset($form_data['folio']) ? $form_data['folio'] : ($dep['folio'] ?? '');
$folio_interno = $error_duplicados && isset($form_data['folio_interno']) ? $form_data['folio_interno'] : ($dep['folio_interno'] ?? '');
$nombre_encargado = $error_duplicados && isset($form_data['nombre_encargado']) ? $form_data['nombre_encargado'] : ($dep['nombre_encargado'] ?? '');
$cargo = $error_duplicados && isset($form_data['cargo']) ? $form_data['cargo'] : ($dep['cargo'] ?? '');
$domicilio = $error_duplicados && isset($form_data['domicilio']) ? $form_data['domicilio'] : ($dep['domicilio'] ?? '');
$cct = $error_duplicados && isset($form_data['cct']) ? $form_data['cct'] : ($dep['cct'] ?? '');
?>
<div class="page-header d-flex justify-content-between align-items-center mb-4">
	<h2 class="mb-0">Editar escuela</h2>
	<a class="btn btn-secondary" href="?page=consultar">← Volver</a>
</div>

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
				<strong>📞 Teléfono:</strong> <?= htmlspecialchars($telefono ?? '', ENT_QUOTES, 'UTF-8') ?> ya está registrado en: <strong><?= htmlspecialchars($telefono_existente ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
			</div>
			<?php endif; ?>
			<?php if (in_array('cct', $campos_duplicados)): ?>
			<div class="mb-2 p-2" style="background-color: #f8d7da; border: 2px solid #dc3545; border-radius: 6px;">
				<strong>🏫 CCT:</strong> <?= htmlspecialchars($cct ?? '', ENT_QUOTES, 'UTF-8') ?> ya está registrado en: <strong><?= htmlspecialchars($cct_existente ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
			</div>
			<?php endif; ?>
		</div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
</div>
<script>
$(document).ready(function() {
	setTimeout(function() {
		$('#alertaDuplicados').fadeOut(500, function() {
			$(this).remove();
		});
	}, 5000);
	
	// Resaltar campos duplicados
	<?php if (in_array('telefono', $campos_duplicados)): ?>
	$('#telefono_editar').css({
		'border': '3px solid #dc3545',
		'background-color': '#fff5f5',
		'box-shadow': '0 0 0 0.2rem rgba(220, 53, 69, 0.25)'
	}).focus();
	<?php endif; ?>
	<?php if (in_array('cct', $campos_duplicados)): ?>
	$('#cct_editar').css({
		'border': '3px solid #dc3545',
		'background-color': '#fff5f5',
		'box-shadow': '0 0 0 0.2rem rgba(220, 53, 69, 0.25)'
	});
	<?php if (!in_array('telefono', $campos_duplicados)): ?>
	$('#cct_editar').focus();
	<?php endif; ?>
	<?php endif; ?>
});
</script>
<?php endif; ?>

<div class="card">
	<div class="card-body">
		<form action="editar_departamento.php" method="post" class="form-grid" id="formEditarDepartamento">
			<input type="hidden" name="id" value="<?= $dep['id_departamento'] ?>">
			<div class="mb-3">
				<label class="form-label">Nombre escuela</label>
				<input type="text" name="nombre_departamento" class="form-control" value="<?= htmlspecialchars((string)$nombre_departamento, ENT_QUOTES, 'UTF-8') ?>" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Teléfono</label>
				<input type="text" name="telefono" id="telefono_editar" class="form-control" value="<?= htmlspecialchars((string)$telefono, ENT_QUOTES, 'UTF-8') ?>" maxlength="10">
				<small class="text-muted">Máximo 10 dígitos</small>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Folio</label>
					<input type="text" name="folio" id="folio_editar" class="form-control" value="<?= htmlspecialchars((string)($dep['folio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" readonly disabled style="background-color: #e9ecef;">
					<small class="text-muted">No se puede modificar</small>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Folio interno</label>
					<input type="text" name="folio_interno" id="folio_interno_editar" class="form-control" value="<?= htmlspecialchars((string)($dep['folio_interno'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" readonly disabled style="background-color: #e9ecef;">
					<small class="text-muted">No se puede modificar</small>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Encargado</label>
					<input type="text" name="nombre_encargado" class="form-control" value="<?= htmlspecialchars((string)$nombre_encargado, ENT_QUOTES, 'UTF-8') ?>">
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Cargo</label>
					<input type="text" name="cargo" class="form-control" value="<?= htmlspecialchars((string)$cargo, ENT_QUOTES, 'UTF-8') ?>">
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label">Domicilio</label>
				<input type="text" name="domicilio" class="form-control" value="<?= htmlspecialchars((string)$domicilio, ENT_QUOTES, 'UTF-8') ?>">
			</div>
			<div class="mb-3">
				<label class="form-label">CCT</label>
				<input type="text" name="cct" id="cct_editar" class="form-control" value="<?= htmlspecialchars((string)$cct, ENT_QUOTES, 'UTF-8') ?>">
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">Guardar cambios</button>
				<a class="btn btn-outline-secondary" href="?page=consultar">Cancelar</a>
			</div>
		</form>
	</div>
</div>

<script>
$(document).ready(function() {
	// Convertir CCT a mayúsculas automáticamente
	$('#cct_editar').on('input', function() {
		$(this).val($(this).val().toUpperCase());
	});
	
	// Limitar teléfono a solo números y máximo 10 dígitos
	$('#telefono_editar').on('input', function() {
		var valor = $(this).val().replace(/\D/g, ''); // Solo números
		if (valor.length > 10) {
			valor = valor.substring(0, 10);
		}
		$(this).val(valor);
	});
	
	// Habilitar los campos disabled antes de enviar el formulario
	$('#formEditarDepartamento').on('submit', function() {
		$('#folio_editar').prop('disabled', false);
		$('#folio_interno_editar').prop('disabled', false);
	});
});
</script>


