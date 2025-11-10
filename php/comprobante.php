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
				<form action="generar_comprobante.php" method="post" id="formGenerarComprobante">
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
                                <?php for($a=2025; $a<=2040; $a++) echo "<option value='$a'>$a</option>"; ?>
                            </select>
                        </div>
					</div>
					<div class="mb-3">
						<label class="form-label">Cantidad a pagar:</label>
						<input type="number" name="cantidad" id="cantidad_pagar" class="form-control" step="0.01" required>
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

<script>
$(document).ready(function() {
	// Interceptar el envío del formulario para mostrar confirmación
	$('#formGenerarComprobante').on('submit', function(e) {
		e.preventDefault();
		
		var cantidad = parseFloat($('#cantidad_pagar').val()) || 0;
		var nombreDepartamento = $('#nombre_dep').val() || 'N/A';
		var cantidadFormateada = '$' + cantidad.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
		
		// Crear modal de confirmación
		var modalConfirmacionHtml = '<div class="modal fade" id="modalConfirmarGenerar" tabindex="-1" aria-labelledby="modalConfirmarGenerarLabel" aria-hidden="true">';
		modalConfirmacionHtml += '<div class="modal-dialog modal-dialog-centered">';
		modalConfirmacionHtml += '<div class="modal-content" style="border-radius: 12px; border: 2px solid #dc3545;">';
		modalConfirmacionHtml += '<div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border-radius: 10px 10px 0 0;">';
		modalConfirmacionHtml += '<h5 class="modal-title" id="modalConfirmarGenerarLabel" style="font-weight: bold;">⚠️ Confirmar Generación de Comprobante</h5>';
		modalConfirmacionHtml += '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '<div class="modal-body" style="padding: 30px;">';
		modalConfirmacionHtml += '<div class="text-center mb-4">';
		modalConfirmacionHtml += '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#dc3545" viewBox="0 0 16 16">';
		modalConfirmacionHtml += '<path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>';
		modalConfirmacionHtml += '</svg>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '<p class="text-center mb-3" style="font-size: 18px; font-weight: 600; color: #333;">¿Estás seguro de que la cantidad a pagar es correcta?</p>';
		modalConfirmacionHtml += '<div class="alert alert-warning" style="background-color: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 15px;">';
		modalConfirmacionHtml += '<p class="mb-2" style="color: #856404; font-weight: 600;"><strong>Departamento:</strong> ' + nombreDepartamento + '</p>';
		modalConfirmacionHtml += '<p class="mb-0" style="color: #856404; font-size: 20px; font-weight: bold;">';
		modalConfirmacionHtml += '<strong>Cantidad a pagar:</strong> <span style="color: #dc3545; font-size: 24px;">' + cantidadFormateada + '</span>';
		modalConfirmacionHtml += '</p>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '<div class="alert alert-danger" style="background-color: #f8d7da; border: 2px solid #dc3545; border-radius: 8px; padding: 15px; margin-top: 15px;">';
		modalConfirmacionHtml += '<p class="mb-0" style="color: #721c24; font-weight: 600; text-align: center;">';
		modalConfirmacionHtml += '⚠️ Una vez registrada, ya no se puede modificar';
		modalConfirmacionHtml += '</p>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '<div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 20px;">';
		modalConfirmacionHtml += '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 10px 20px;">Cancelar</button>';
		modalConfirmacionHtml += '<button type="button" id="btnConfirmarGenerar" class="btn btn-danger" style="border-radius: 8px; padding: 10px 20px; font-weight: bold;">Sí, generar comprobante</button>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '</div>';
		
		// Remover modal anterior si existe
		$('#modalConfirmarGenerar').remove();
		
		// Agregar modal al body
		$('body').append(modalConfirmacionHtml);
		
		// Mostrar modal
		var modalConfirmar = new bootstrap.Modal(document.getElementById('modalConfirmarGenerar'));
		modalConfirmar.show();
		
		// Al confirmar, enviar el formulario
		$('#btnConfirmarGenerar').on('click', function() {
			$('#formGenerarComprobante').off('submit').submit();
		});
		
		// Limpiar modal al cerrar
		$('#modalConfirmarGenerar').on('hidden.bs.modal', function() {
			$(this).remove();
		});
	});
});
</script>
