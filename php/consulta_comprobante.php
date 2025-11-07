<div class="page-header mb-4">
	<h2 class="mb-0">Consulta de comprobante Telmex</h2>
</div>

<!-- Formulario de búsqueda -->
<div class="card">
	<div class="card-body">
		<form id="formBuscarComprobante" method="post" action="buscar_comprobante.php">
			<div class="row g-3 mb-4">
				<div class="col-md-4">
					<label class="form-label">Teléfono:</label>
					<input type="text" name="telefono" id="telefono" class="form-control" placeholder="Teléfono" required>
				</div>
				<div class="col-md-4">
					<label class="form-label">Mes:</label>
					<select name="mes" id="mes" class="form-select" required>
						<option value="">Selecciona mes</option>
						<?php 
						$meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
						foreach($meses as $mes) echo "<option value='$mes'>$mes</option>";
						?>
					</select>
				</div>
				<div class="col-md-4">
					<label class="form-label">Año:</label>
					<select name="anio" id="anio" class="form-select" required>
						<option value="">Selecciona año</option>
						<?php for($a=2023; $a<=2030; $a++) echo "<option value='$a'>$a</option>"; ?>
					</select>
				</div>
			</div>
			<button type="submit" class="btn btn-primary">Buscar</button>
		</form>
	</div>
</div>

<!-- Área para mostrar resultados -->
<div id="resultadoBusqueda" style="display: none;"></div>

<!-- Área para alertas temporales -->
<div id="alertaTemporal" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: none;"></div>

<script>
$(document).ready(function() {
	// Función para limpiar el formulario y ocultar resultados
	function limpiarFormulario() {
		$('#telefono').val('');
		$('#mes').val('');
		$('#anio').val('');
		$('#resultadoBusqueda').hide().html('');
	}
	
	// Función para mostrar alerta temporal
	function mostrarAlertaTemporal(mensaje, tipo) {
		tipo = tipo || 'warning';
		var bgColor = tipo === 'danger' ? '#dc3545' : '#ffc107';
		var textColor = tipo === 'danger' ? 'white' : '#000';
		
		var html = '<div class="alert alert-' + tipo + ' shadow-lg" role="alert" style="background-color: ' + bgColor + '; color: ' + textColor + '; border: 2px solid ' + (tipo === 'danger' ? '#c82333' : '#ffb300') + '; border-radius: 8px; padding: 15px 20px; min-width: 300px; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">';
		html += '<div class="d-flex align-items-center">';
		html += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill me-2" viewBox="0 0 16 16">';
		html += '<path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>';
		html += '</svg>';
		html += '<strong style="margin-left: 8px;">' + mensaje + '</strong>';
		html += '</div>';
		html += '</div>';
		
		$('#alertaTemporal').html(html).fadeIn(300);
		
		// Ocultar después de 3 segundos
		setTimeout(function() {
			$('#alertaTemporal').fadeOut(300, function() {
				$(this).html('');
			});
		}, 3000);
	}
	
	$('#formBuscarComprobante').on('submit', function(e) {
		e.preventDefault();
		
		var formData = $(this).serialize();
		
		$.ajax({
			url: 'buscar_comprobante.php',
			type: 'POST',
			data: formData,
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					if (response.tiene_comprobante === true && response.comprobante) {
						// Mostrar recuadro con información
						var html = '<div class="card mt-4">';
						html += '<div class="card-body">';
						html += '<h5 class="card-title mb-4">Información del Comprobante</h5>';
						html += '<div class="row mb-3">';
						html += '<div class="col-md-6"><strong>Nombre del Departamento:</strong></div>';
						html += '<div class="col-md-6">' + response.departamento.nombre_departamento + '</div>';
						html += '</div>';
						html += '<div class="row mb-3">';
						html += '<div class="col-md-6"><strong>Teléfono:</strong></div>';
						html += '<div class="col-md-6">' + response.departamento.telefono + '</div>';
						html += '</div>';
						html += '<div class="row mb-3">';
						html += '<div class="col-md-6"><strong>CCT:</strong></div>';
						html += '<div class="col-md-6">' + (response.departamento.cct || 'N/A') + '</div>';
						html += '</div>';
						html += '<div class="row mb-3">';
						html += '<div class="col-md-6"><strong>Cantidad a Pagar:</strong></div>';
						html += '<div class="col-md-6">$' + parseFloat(response.comprobante.total_pagar).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</div>';
						html += '</div>';
						html += '<div class="row mb-3">';
						html += '<div class="col-md-6"><strong>Descargar Comprobante:</strong></div>';
						html += '<div class="col-md-6">';
						html += '<a href="pdf_escuela.php?id_pago=' + response.comprobante.id_pago + '&id_departamento=' + response.departamento.id_departamento + '" class="btn btn-danger" target="_blank" style="display: inline-flex; align-items: center; gap: 8px;">';
						html += '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">';
						html += '<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>';
						html += '<path d="M4.603 12.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.852.192-.113.407-.17.618-.17.22 0 .445.062.656.17.376.18.576.498.652.852.073.34.04.736-.046 1.136a7.272 7.272 0 0 1-.43 1.295 19.684 19.684 0 0 0 1.062 2.227c.17.25.339.5.5.758a7.68 7.68 0 0 1 .897.787c.37.22.699.48.897.787.21.326.275.714.08 1.102a.816.816 0 0 1-.437.42c-.11.052-.22.078-.33.078s-.22-.026-.33-.078z"/>';
						html += '</svg>';
						html += '<span>Descargar PDF</span>';
						html += '</a>';
						html += '</div>';
						html += '</div>';
						html += '<div class="mt-4 pt-3 border-top text-center">';
						html += '<button type="button" class="btn btn-secondary btnCerrarResultado">Cerrar</button>';
						html += '</div>';
						html += '</div>';
						html += '</div>';
						
						$('#resultadoBusqueda').html(html).show();
					} else {
						// Mostrar alerta de que no hay comprobante (diseño rojo y blanco)
						// Esto se ejecuta cuando tiene_comprobante es false o no hay comprobante
						var mesSeleccionado = $('#mes option:selected').text() || $('#mes').val();
						var anioSeleccionado = $('#anio').val();
						
						var html = '<div class="card mt-4 shadow-lg" style="border: 3px solid #dc3545; border-radius: 10px; overflow: hidden;">';
						html += '<div class="card-body" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 30px;">';
						html += '<div class="d-flex align-items-start">';
						html += '<div style="flex-shrink: 0; margin-right: 20px;">';
						html += '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="white" viewBox="0 0 16 16" style="opacity: 0.95;">';
						html += '<path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>';
						html += '</svg>';
						html += '</div>';
						html += '<div style="flex: 1;">';
						html += '<h4 class="mb-3" style="color: white; font-weight: bold; margin: 0; font-size: 22px;">⚠️ Aviso Importante</h4>';
						html += '<p class="mb-2" style="color: white; font-size: 18px; line-height: 1.6; font-weight: 500;">';
						html += '<strong>Este departamento aún no cuenta con un comprobante de pago generado para el mes y año seleccionados.</strong>';
						html += '</p>';
						if (mesSeleccionado && anioSeleccionado) {
							html += '<p class="mb-2" style="color: #ffcccc; font-size: 15px; margin-top: 10px;">';
							html += '<strong>Período consultado:</strong> ' + mesSeleccionado + ' ' + anioSeleccionado;
							html += '</p>';
						}
						if (response.departamento && response.departamento.nombre_departamento) {
							html += '<p class="mb-0 mt-2" style="color: #ffcccc; font-size: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.3);">';
							html += '<strong>Departamento:</strong> ' + response.departamento.nombre_departamento;
							html += '</p>';
						}
						html += '</div>';
						html += '</div>';
						html += '<div class="mt-4 pt-3 border-top text-center" style="border-top: 1px solid rgba(255,255,255,0.3) !important;">';
						html += '<button type="button" class="btn btn-light btnCerrarResultado" style="color: #dc3545; font-weight: bold;">Cerrar</button>';
						html += '</div>';
						html += '</div>';
						html += '</div>';
						
						$('#resultadoBusqueda').html(html).show();
					}
				} else {
					// Verificar si es el error de teléfono no encontrado
					if (response.message && (response.message.includes('teléfono') || response.message.includes('telefono') || response.message.includes('No se encontró'))) {
						mostrarAlertaTemporal('Ese número no se encuentra registrado', 'danger');
					} else {
						// Mostrar otros errores
						var html = '<div class="alert alert-danger mt-4" role="alert">' + response.message + '</div>';
						html += '<div class="mt-3 text-center">';
						html += '<button type="button" class="btn btn-secondary btnCerrarResultado">Cerrar</button>';
						html += '</div>';
						$('#resultadoBusqueda').html(html).show();
					}
				}
			},
			error: function(xhr, status, error) {
				var html = '<div class="alert alert-danger mt-4" role="alert">Error al realizar la búsqueda. Por favor, intente nuevamente.</div>';
				html += '<div class="mt-3 text-center">';
				html += '<button type="button" class="btn btn-secondary btnCerrarResultado">Cerrar</button>';
				html += '</div>';
				$('#resultadoBusqueda').html(html).show();
			}
		});
	});
	
	// Evento delegado para el botón cerrar (funciona con elementos dinámicos)
	$(document).on('click', '.btnCerrarResultado', function() {
		limpiarFormulario();
	});
});
</script>

