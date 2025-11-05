<?php include 'conexion_base.php'; ?>
<div class="page-header">
	<h2>Agregar nueva escuela</h2>
	<a class="btn btn-secondary" href="?page=consultar">← Volver</a>
</div>

<div class="card">
	<form action="agregar_departamento.php" method="post" class="form-grid">
		<div class="form-group">
			<label>Nombre escuela</label>
			<input type="text" name="nombre_departamento" placeholder="Nombre escuela" required>
		</div>
		<div class="form-group">
			<label>Teléfono</label>
			<input type="text" name="telefono" placeholder="Teléfono">
		</div>
		<div class="form-row">
			<div class="form-group">
				<label>Folio</label>
				<input type="text" name="folio" placeholder="Folio">
			</div>
			<div class="form-group">
				<label>Folio interno</label>
				<input type="text" name="folio_interno" placeholder="Folio interno">
			</div>
		</div>
		<div class="form-row">
			<div class="form-group">
				<label>Encargado</label>
				<input type="text" name="nombre_encargado" placeholder="Nombre del encargado">
			</div>
			<div class="form-group">
				<label>Cargo</label>
				<input type="text" name="cargo" placeholder="Cargo">
			</div>
		</div>
		<div class="form-group">
			<label>Domicilio</label>
			<input type="text" name="domicilio" placeholder="Domicilio">
		</div>
		<div class="form-group">
			<label>CCT</label>
			<input type="text" name="cct" placeholder="CCT">
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Guardar</button>
			<a class="btn btn-link" href="?page=consultar">Cancelar</a>
		</div>
	</form>
</div>


