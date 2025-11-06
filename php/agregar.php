<?php include 'conexion_base.php'; ?>
<div class="page-header d-flex justify-content-between align-items-center mb-4">
	<h2 class="mb-0">Agregar nueva escuela</h2>
	<a class="btn btn-secondary" href="?page=consultar">← Volver</a>
</div>

<div class="card">
	<div class="card-body">
		<form action="agregar_departamento.php" method="post" class="form-grid">
			<div class="mb-3">
				<label class="form-label">Nombre escuela</label>
				<input type="text" name="nombre_departamento" class="form-control" placeholder="Nombre escuela" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Teléfono</label>
				<input type="text" name="telefono" class="form-control" placeholder="Teléfono">
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Folio</label>
					<input type="text" name="folio" class="form-control" placeholder="Folio">
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Folio interno</label>
					<input type="text" name="folio_interno" class="form-control" placeholder="Folio interno">
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Encargado</label>
					<input type="text" name="nombre_encargado" class="form-control" placeholder="Nombre del encargado">
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Cargo</label>
					<input type="text" name="cargo" class="form-control" placeholder="Cargo">
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label">Domicilio</label>
				<input type="text" name="domicilio" class="form-control" placeholder="Domicilio">
			</div>
			<div class="mb-3">
				<label class="form-label">CCT</label>
				<input type="text" name="cct" class="form-control" placeholder="CCT">
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">Guardar</button>
				<a class="btn btn-outline-secondary" href="?page=consultar">Cancelar</a>
			</div>
		</form>
	</div>
</div>


