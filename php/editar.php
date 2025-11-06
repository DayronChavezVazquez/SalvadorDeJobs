<?php include 'conexion_base.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare('SELECT * FROM ct_departamentos WHERE id_departamento = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$dep = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$dep) { echo '<p>No encontrado</p>'; return; }
?>
<div class="page-header d-flex justify-content-between align-items-center mb-4">
	<h2 class="mb-0">Editar escuela</h2>
	<a class="btn btn-secondary" href="?page=consultar">← Volver</a>
</div>

<div class="card">
	<div class="card-body">
		<form action="editar_departamento.php" method="post" class="form-grid">
			<input type="hidden" name="id" value="<?= $dep['id_departamento'] ?>">
			<div class="mb-3">
				<label class="form-label">Nombre escuela</label>
				<input type="text" name="nombre_departamento" class="form-control" value="<?= htmlspecialchars((string)($dep['nombre_departamento'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Teléfono</label>
				<input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars((string)($dep['telefono'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Folio</label>
					<input type="text" name="folio" class="form-control" value="<?= htmlspecialchars((string)($dep['folio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Folio interno</label>
					<input type="text" name="folio_interno" class="form-control" value="<?= htmlspecialchars((string)($dep['folio_interno'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Encargado</label>
					<input type="text" name="nombre_encargado" class="form-control" value="<?= htmlspecialchars((string)($dep['nombre_encargado'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Cargo</label>
					<input type="text" name="cargo" class="form-control" value="<?= htmlspecialchars((string)($dep['cargo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label">Domicilio</label>
				<input type="text" name="domicilio" class="form-control" value="<?= htmlspecialchars((string)($dep['domicilio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
			</div>
			<div class="mb-3">
				<label class="form-label">CCT</label>
				<input type="text" name="cct" class="form-control" value="<?= htmlspecialchars((string)($dep['cct'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">Guardar cambios</button>
				<a class="btn btn-outline-secondary" href="?page=consultar">Cancelar</a>
			</div>
		</form>
	</div>
</div>


