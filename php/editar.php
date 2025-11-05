<?php include 'conexion_base.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare('SELECT * FROM ct_departamentos WHERE id_departamento = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$dep = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$dep) { echo '<p>No encontrado</p>'; return; }
?>
<div class="page-header">
	<h2>Editar escuela</h2>
	<a class="btn btn-secondary" href="?page=consultar">← Volver</a>
</div>

<div class="card">
    <form action="editar_departamento.php" method="post" class="form-grid">
		<input type="hidden" name="id" value="<?= $dep['id_departamento'] ?>">
		<div class="form-group">
			<label>Nombre escuela</label>
            <input type="text" name="nombre_departamento" value="<?= htmlspecialchars((string)($dep['nombre_departamento'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
		</div>
		<div class="form-group">
			<label>Teléfono</label>
            <input type="text" name="telefono" value="<?= htmlspecialchars((string)($dep['telefono'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
		</div>
		<div class="form-row">
			<div class="form-group">
				<label>Folio</label>
                <input type="text" name="folio" value="<?= htmlspecialchars((string)($dep['folio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
			</div>
			<div class="form-group">
				<label>Folio interno</label>
                <input type="text" name="folio_interno" value="<?= htmlspecialchars((string)($dep['folio_interno'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
			</div>
		</div>
		<div class="form-row">
			<div class="form-group">
				<label>Encargado</label>
                <input type="text" name="nombre_encargado" value="<?= htmlspecialchars((string)($dep['nombre_encargado'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
			</div>
			<div class="form-group">
				<label>Cargo</label>
                <input type="text" name="cargo" value="<?= htmlspecialchars((string)($dep['cargo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
			</div>
		</div>
		<div class="form-group">
			<label>Domicilio</label>
            <input type="text" name="domicilio" value="<?= htmlspecialchars((string)($dep['domicilio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
		</div>
		<div class="form-group">
			<label>CCT</label>
            <input type="text" name="cct" value="<?= htmlspecialchars((string)($dep['cct'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Guardar cambios</button>
			<a class="btn btn-link" href="?page=consultar">Cancelar</a>
		</div>
	</form>
</div>


