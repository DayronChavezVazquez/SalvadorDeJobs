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
                                <?php 
                                $anio_actual = (int)date('Y');
                                echo "<option value='$anio_actual' selected>$anio_actual</option>";
                                ?>
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
		modalConfirmacionHtml += '<div class="modal-dialog modal-dialog-centered" style="display: flex; align-items: center; justify-content: center; min-height: calc(100% - 3.5rem);">';
		modalConfirmacionHtml += '<div class="modal-content" style="border-radius: 12px; border: 3px solid #ffc107; box-shadow: 0 10px 40px rgba(255, 193, 7, 0.3);">';
		modalConfirmacionHtml += '<div class="modal-header" style="background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%); color: #856404; border-radius: 9px 9px 0 0; border-bottom: 2px solid #ffb300;">';
		modalConfirmacionHtml += '<h5 class="modal-title" id="modalConfirmarGenerarLabel" style="font-weight: bold; color: #856404;">⚠️ Confirmar Generación de Comprobante</h5>';
		modalConfirmacionHtml += '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) saturate(100%) invert(30%) sepia(95%) saturate(1000%) hue-rotate(20deg);"></button>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '<div class="modal-body" style="padding: 30px; background: linear-gradient(135deg, #fff9e6 0%, #fffef0 100%);">';
		modalConfirmacionHtml += '<div class="text-center mb-4">';
		modalConfirmacionHtml += '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ffc107" viewBox="0 0 16 16" style="filter: drop-shadow(0 2px 4px rgba(255, 193, 7, 0.3));">';
		modalConfirmacionHtml += '<path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>';
		modalConfirmacionHtml += '</svg>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '<p class="text-center mb-3" style="font-size: 18px; font-weight: 600; color: #856404;">¿Estás seguro de que la cantidad a pagar es correcta?</p>';
		modalConfirmacionHtml += '<div class="alert" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 3px solid #ffc107; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(255, 193, 7, 0.2);">';
		modalConfirmacionHtml += '<p class="mb-2" style="color: #856404; font-weight: 600; font-size: 16px;"><strong>Departamento:</strong> ' + nombreDepartamento + '</p>';
		modalConfirmacionHtml += '<p class="mb-0" style="color: #856404; font-size: 22px; font-weight: bold;">';
		modalConfirmacionHtml += '<strong>Cantidad a pagar:</strong> <span style="color: #d68910; font-size: 28px; text-shadow: 0 2px 4px rgba(214, 137, 16, 0.3);">' + cantidadFormateada + '</span>';
		modalConfirmacionHtml += '</p>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '<div class="alert" style="background: linear-gradient(135deg, #ffeaa7 0%, #ffd93d 100%); border: 2px solid #ffc107; border-radius: 8px; padding: 15px; margin-top: 15px; box-shadow: 0 2px 6px rgba(255, 193, 7, 0.2);">';
		modalConfirmacionHtml += '<p class="mb-0" style="color: #856404; font-weight: 600; text-align: center; font-size: 15px;">';
		modalConfirmacionHtml += '⚠️ Una vez registrada, ya no se puede modificar';
		modalConfirmacionHtml += '</p>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '</div>';
		modalConfirmacionHtml += '<div class="modal-footer" style="border-top: 2px solid #ffc107; padding: 20px; background: linear-gradient(135deg, #fff9e6 0%, #fffef0 100%); border-radius: 0 0 9px 9px;">';
		modalConfirmacionHtml += '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 10px 20px;">Cancelar</button>';
		modalConfirmacionHtml += '<button type="button" id="btnConfirmarGenerar" class="btn" style="background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%); color: #856404; border: 2px solid #ffb300; border-radius: 8px; padding: 10px 20px; font-weight: bold; box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);">Sí, generar comprobante</button>';
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
