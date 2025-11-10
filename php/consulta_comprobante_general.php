<div class="page-header mb-4">
    <h2 class="mb-0">Consulta de comprobante general</h2>
</div>

<div class="card">
    <div class="card-body">
        <form id="formBuscarComprobanteGeneral" method="post" action="buscar_comprobante_general.php">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Mes:</label>
                    <select name="mes" id="mes_general" class="form-select" required>
                        <option value="">Selecciona mes</option>
                        <?php 
                        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                        foreach($meses as $mes) echo "<option value='$mes'>$mes</option>";
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Año:</label>
                    <select name="anio" id="anio_general" class="form-select" required>
                        <option value="">Selecciona año</option>
                        <?php for($a=2025; $a<=2040; $a++) echo "<option value='$a'>$a</option>"; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
    </div>
</div>

<div id="resultadoBusquedaGeneral" style="display: none;"></div>

<div id="alertaTemporalGeneral" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: none;"></div>

<script>
$(document).ready(function() {
    function mostrarAlertaTemporalGeneral(mensaje, tipo) {
        tipo = tipo || 'warning';
        var bgColor = tipo === 'danger' ? '#dc3545' : '#17a2b8';
        var textColor = 'white';

        var html = '<div class="alert alert-' + tipo + ' shadow-lg" role="alert" style="background-color: ' + bgColor + '; color: ' + textColor + '; border: 2px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 15px 20px; min-width: 320px; max-width: 420px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">';
        html += '<div class="d-flex align-items-center">';
        html += '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-info-circle-fill me-2" viewBox="0 0 16 16">';
        html += '<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>';
        html += '</svg>';
        html += '<strong style="margin-left: 8px;">' + mensaje + '</strong>';
        html += '</div></div>';

        $('#alertaTemporalGeneral').html(html).fadeIn(300);
        setTimeout(function() {
            $('#alertaTemporalGeneral').fadeOut(300, function() {
                $(this).html('');
            });
        }, 3000);
    }

    $('#formBuscarComprobanteGeneral').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: 'buscar_comprobante_general.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.sin_registros_pagos === true) {
                        var mesSeleccionado = $('#mes_general option:selected').text() || $('#mes_general').val();
                        var anioSeleccionado = $('#anio_general').val();
                        mostrarAlertaTemporalGeneral('No hay registros para el mes y año seleccionados.', 'warning');
                        $('#resultadoBusquedaGeneral').hide().html('');
                        return;
                    }
                    if (response.sin_registros === true) {
                        var mesSeleccionado = $('#mes_general option:selected').text() || $('#mes_general').val();
                        var anioSeleccionado = $('#anio_general').val();
                        mostrarAlertaTemporalGeneral('No se cuenta con información registrada para el mes y año seleccionados.', 'warning');
                        $('#resultadoBusquedaGeneral').hide().html('');
                        return;
                    }
                    if (response.departamentos && response.departamentos.length > 0) {
                        var totalGeneral = parseFloat(response.total_general || 0);
                        var html = '<div class="card mt-4">';
                        html += '<div class="card-body">';
                        html += '<div class="d-flex justify-content-between align-items-center mb-3">';
                        html += '<h5 class="card-title mb-0">Departamento(s) encontrados</h5>';
                        html += '<a href="pdf_comprobante_general.php?mes=' + encodeURIComponent(response.mes) + '&anio=' + encodeURIComponent(response.anio) + '" class="btn btn-danger" target="_blank" style="display: inline-flex; align-items: center; gap: 8px;">';
                        html += '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">';
                        html += '<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>';
                        html += '<path d="M4.603 12.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.852.192-.113.407-.17.618-.17.22 0 .445.062.656.17.376.18.576.498.652.852.073.34.04.736-.046 1.136a7.272 7.272 0 0 1-.43 1.295 19.684 19.684 0 0 0 1.062 2.227c.17.25.339.5.5.758a7.68 7.68 0 0 1 .897.787c.37.22.699.48.897.787.21.326.275.714.08 1.102a.816.816 0 0 1-.437.42c-.11.052-.22.078-.33.078s-.22-.026-.33-.078z"/>';
                        html += '</svg>';
                        html += '<span>Descargar PDF general</span>';
                        html += '</a>';
                        html += '</div>';

                        html += '<div class="table-responsive">';
                        html += '<table class="table table-striped table-hover">';
                        html += '<thead class="table-dark">';
                        html += '<tr>';
                        html += '<th>Folio</th>';
                        html += '<th>Nombre del Departamento</th>';
                        html += '<th>Teléfono</th>';
                        html += '<th>CCT</th>';
                        html += '<th>Total a pagar</th>';
                        html += '<th>Estado</th>';
                        html += '</tr>';
                        html += '</thead>';
                        html += '<tbody>';

                        response.departamentos.forEach(function(departamento) {
                            var total = parseFloat(departamento.total_pagar || 0);
                            var tieneComprobante = total > 0;
                            var estadoTexto = tieneComprobante ? 'Comprobante generado' : 'Sin comprobante';
                            var estadoColor = tieneComprobante ? 'success' : 'danger';
                            var estadoBadge = '<span class="badge bg-' + estadoColor + '">' + estadoTexto + '</span>';
                            
                            html += '<tr>';
                            html += '<td>' + (departamento.folio || 'N/A') + '</td>';
                            html += '<td>' + (departamento.nombre_departamento || 'N/A') + '</td>';
                            html += '<td>' + (departamento.telefono || 'N/A') + '</td>';
                            html += '<td>' + (departamento.cct || 'N/A') + '</td>';
                            html += '<td>$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
                            html += '<td>' + estadoBadge + '</td>';
                            html += '</tr>';
                        });

                        html += '</tbody>';
                        html += '<tfoot>';
                        html += '<tr>';
                        html += '<th colspan="5" class="text-end">Total por pagar:</th>';
                        html += '<th>$' + totalGeneral.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</th>';
                        html += '</tr>';
                        html += '</tfoot>';
                        html += '</table>';
                        html += '</div>';

                        html += '<div class="mt-4 pt-3 border-top text-center">';
                        html += '<button type="button" class="btn btn-secondary btnCerrarResultadoGeneral">Cerrar</button>';
                        html += '</div>';
                        html += '</div></div>';

                        $('#resultadoBusquedaGeneral').html(html).show();
                    } else {
                        mostrarAlertaTemporalGeneral('No se encontraron departamentos para el período seleccionado.', 'warning');
                        $('#resultadoBusquedaGeneral').hide().html('');
                    }
                } else {
                    mostrarAlertaTemporalGeneral(response.message || 'Ocurrió un error al realizar la búsqueda.', 'danger');
                    $('#resultadoBusquedaGeneral').hide().html('');
                }
            },
            error: function() {
                mostrarAlertaTemporalGeneral('Error al realizar la búsqueda. Intenta nuevamente.', 'danger');
                $('#resultadoBusquedaGeneral').hide().html('');
            }
        });
    });

    $(document).on('click', '.btnCerrarResultadoGeneral', function() {
        $('#formBuscarComprobanteGeneral')[0].reset();
        $('#resultadoBusquedaGeneral').hide().html('');
    });
});
</script>


