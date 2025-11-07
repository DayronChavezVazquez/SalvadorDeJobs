<div class="page-header mb-4">
	<h2 class="mb-0">Generar comprobante Telmex</h2>
</div>

<!-- Buscador -->
<div class="card">
	<div class="card-body">
		<div class="row g-3 mb-4">
			<div class="col-md-6">
				<label class="form-label">Folio</label>
				<input type="text" id="folio" class="form-control" placeholder="Folio">
			</div>
			<div class="col-md-6">
				<label class="form-label">CCT</label>
				<input type="text" id="cct" class="form-control" placeholder="CCT">
			</div>
		</div>
		<button id="btnBuscar" class="btn btn-primary">Buscar</button>
	</div>
</div>

<!-- Modal Comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1" aria-labelledby="modalComprobanteLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalComprobanteLabel">Generar Comprobante</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form action="generar_comprobante.php" method="post">
					<input type="hidden" name="folio" id="folio_dep">
					<input type="hidden" name="id_departamento" id="id_departamento">
					<div class="mb-3">
						<label class="form-label">Nombre Departamento:</label>
						<input type="text" id="nombre_dep" name="nombre_departamento" class="form-control" readonly>
					</div>
					<div class="mb-3">
						<label class="form-label">Teléfono:</label>
						<input type="text" id="telefono_dep" name="telefono" class="form-control" readonly>
					</div>
					<hr>
					<h5 class="mb-3">Datos del comprobante a generar</h5>
					<div class="mb-3">
						<label class="form-label">Nombre del firmante:</label>
						<input type="text" name="nombre_firmante" class="form-control" placeholder="Nombre completo">
					</div>
					<div class="mb-3">
						<label class="form-label">Puesto del firmante:</label>
						<input type="text" name="puesto_firmante" class="form-control" placeholder="Puesto">
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Mes:</label>
							<select name="mes" class="form-select" required>
								<option value="">Selecciona mes</option>
								<?php 
								$meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
								foreach($meses as $mes) echo "<option value='$mes'>$mes</option>";
								?>
							</select>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Año:</label>
							<select name="anio" class="form-select" required>
								<option value="">Selecciona año</option>
								<?php for($a=2023; $a<=2030; $a++) echo "<option value='$a'>$a</option>"; ?>
							</select>
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label">Cantidad a pagar:</label>
						<input type="number" name="cantidad" class="form-control" step="0.01" required>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
						<button type="submit" class="btn btn-primary">Generar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
