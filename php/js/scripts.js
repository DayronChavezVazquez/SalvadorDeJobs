// scripts.js
// Control de apertura de modales y búsqueda AJAX

$(document).ready(function() {
    console.log('Scripts.js cargado correctamente');
    
    // Abrir modal para agregar departamento (si existe)
    $('#btnAgregar').click(function() {
        var modal = new bootstrap.Modal(document.getElementById('modalAgregar'), {
            backdrop: false,
            keyboard: true,
            focus: true
        });
        modal.show();
    });

    // AJAX para buscar departamento por folio o CCT
    $('#btnBuscar').click(function() {
        let folio = $('#folio').val().trim();
        let cct = $('#cct').val().trim();

        if(folio == '' && cct == '') {
            alert('Ingresa folio o CCT');
            return;
        }

        $.ajax({
            url: 'buscar_departamento.php',
            type: 'GET',
            data: { folio: folio, cct: cct },
            dataType: 'json',
            success: function(data) {
                if(data && data.folio) {
                    $('#nombre_dep').val(data.nombre_departamento || '');
                    $('#telefono_dep').val(data.telefono || '');
                    $('#folio_dep').val(data.folio || '');
                    $('#id_departamento').val(data.id_departamento || '');
                    
                    // Obtener el elemento del modal
                    var modalElement = document.getElementById('modalComprobante');
                    if (modalElement) {
                        // Cerrar cualquier modal abierto previamente
                        var existingModal = bootstrap.Modal.getInstance(modalElement);
                        if (existingModal) {
                            existingModal.hide();
                        }
                        
                        // Crear nueva instancia y mostrar
                        var modalComprobante = new bootstrap.Modal(modalElement, {
                            backdrop: false,
                            keyboard: true,
                            focus: true
                        });
                        modalComprobante.show();
                        
                        // Asegurar que el modal sea interactivo después de mostrarse
                        setTimeout(function() {
                            modalElement.style.pointerEvents = 'auto';
                            var modalContent = modalElement.querySelector('.modal-content');
                            if (modalContent) {
                                modalContent.style.pointerEvents = 'auto';
                            }
                        }, 100);
                    } else {
                        alert('Error: No se encontró el modal');
                    }
                } else {
                    alert('No se encontró ningún departamento con esos datos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la búsqueda:', error);
                alert('Error al buscar el departamento. Por favor, intenta de nuevo.');
            }
        });
    });

    // Ocultar toasts después de 1 segundo
    const $toastSuccess = $('#toast-success');
    if ($toastSuccess.length) {
        setTimeout(() => { $toastSuccess.fadeOut(400); }, 1000);
    }
    const $toastUpdated = $('#toast-updated');
    if ($toastUpdated.length) {
        setTimeout(() => { $toastUpdated.fadeOut(400); }, 1000);
    }
    const $toastDeleted = $('#toast-deleted');
    if ($toastDeleted.length) {
        setTimeout(() => { $toastDeleted.fadeOut(400); }, 1000);
    }

    // Variables para controlar los timeouts de búsqueda
    var timeoutNombre = null;
    var timeoutTelefono = null;
    var ultimoValorNombre = '';
    var ultimoValorTelefono = '';

    // Función para limpiar todos los filtros
    function limpiarFiltros() {
        // Limpiar campos
        $('#search-nombre').val('');
        $('#search-telefono').val('');
        
        // Limpiar sessionStorage
        sessionStorage.removeItem('search-nombre');
        sessionStorage.removeItem('search-telefono');
        sessionStorage.removeItem('campo-activo');
        
        // Limpiar variables
        ultimoValorNombre = '';
        ultimoValorTelefono = '';
        
        // Limpiar timeouts
        if (timeoutNombre) clearTimeout(timeoutNombre);
        if (timeoutTelefono) clearTimeout(timeoutTelefono);
        
        // Redirigir sin filtros
        window.location.href = '?page=consultar';
    }

    // Función simple para ejecutar búsqueda preservando valores
    function hacerBusqueda() {
        var nombre = $('#search-nombre').length ? $('#search-nombre').val() || '' : '';
        var telefono = $('#search-telefono').length ? $('#search-telefono').val() || '' : '';
        
        // Guardar valores en sessionStorage para preservarlos después de la redirección
        if (nombre.trim()) {
            sessionStorage.setItem('search-nombre', nombre.trim());
        } else {
            sessionStorage.removeItem('search-nombre');
        }
        if (telefono.trim()) {
            sessionStorage.setItem('search-telefono', telefono.trim());
        } else {
            sessionStorage.removeItem('search-telefono');
        }
        
        console.log('Ejecutando búsqueda - Nombre:', nombre, 'Teléfono:', telefono);
        
        // Construir URL simple
        var url = '?page=consultar';
        if (nombre.trim() !== '') {
            url += '&q=' + encodeURIComponent(nombre.trim());
        }
        if (telefono.trim() !== '') {
            url += '&q_tel=' + encodeURIComponent(telefono.trim());
        }
        
        console.log('Redirigiendo a:', url);
        // Redirigir
        window.location.href = url;
    }
    
    // Restaurar valores de búsqueda y mantener el foco después de la redirección
    setTimeout(function() {
        var urlParams = new URLSearchParams(window.location.search);
        var q = urlParams.get('q') || '';
        var q_tel = urlParams.get('q_tel') || '';
        
        // Si no hay filtros en la URL, limpiar sessionStorage
        if (!q && !q_tel) {
            sessionStorage.removeItem('search-nombre');
            sessionStorage.removeItem('search-telefono');
            sessionStorage.removeItem('campo-activo');
            ultimoValorNombre = '';
            ultimoValorTelefono = '';
        } else {
            // Restaurar valores desde la URL o sessionStorage
            var searchNombre = q || sessionStorage.getItem('search-nombre') || '';
            var searchTelefono = q_tel || sessionStorage.getItem('search-telefono') || '';
            var campoActivo = sessionStorage.getItem('campo-activo');
            
            // Restaurar valores si existen
            if (searchNombre && $('#search-nombre').length) {
                $('#search-nombre').val(searchNombre);
                ultimoValorNombre = searchNombre.trim();
            }
            if (searchTelefono && $('#search-telefono').length) {
                $('#search-telefono').val(searchTelefono);
                ultimoValorTelefono = searchTelefono.trim();
            }
            
            // Restaurar el foco al campo que estaba activo
            if (campoActivo === 'nombre' && $('#search-nombre').length) {
                var campo = $('#search-nombre')[0];
                var len = campo.value.length;
                campo.focus();
                campo.setSelectionRange(len, len); // Cursor al final
            } else if (campoActivo === 'telefono' && $('#search-telefono').length) {
                var campo = $('#search-telefono')[0];
                var len = campo.value.length;
                campo.focus();
                campo.setSelectionRange(len, len); // Cursor al final
            }
        }
    }, 100);

    // Usar delegación de eventos para asegurar que funcione incluso si los elementos se cargan después
    $(document).on('input', '#search-nombre', function() {
        var valor = $(this).val();
        sessionStorage.setItem('campo-activo', 'nombre');
        console.log('Escribiendo en nombre:', valor);
        
        // Limpiar timeout anterior
        if (timeoutNombre) {
            clearTimeout(timeoutNombre);
        }
        
        // Solo ejecutar búsqueda si el valor cambió significativamente
        // Aumentar tiempo de espera a 800ms para permitir eliminar texto
        timeoutNombre = setTimeout(function() {
            var valorActual = $('#search-nombre').val() || '';
            // Solo ejecutar si el valor es diferente al último valor procesado
            if (valorActual.trim() !== ultimoValorNombre.trim()) {
                ultimoValorNombre = valorActual.trim();
                console.log('Timeout ejecutado para nombre');
                hacerBusqueda();
            }
        }, 800);
    });

    // Usar delegación de eventos para teléfono
    $(document).on('input', '#search-telefono', function() {
        var valor = $(this).val();
        sessionStorage.setItem('campo-activo', 'telefono');
        console.log('Escribiendo en teléfono:', valor);
        
        // Limpiar timeout anterior
        if (timeoutTelefono) {
            clearTimeout(timeoutTelefono);
        }
        
        // Solo ejecutar búsqueda si el valor cambió significativamente
        // Aumentar tiempo de espera a 800ms para permitir eliminar texto
        timeoutTelefono = setTimeout(function() {
            var valorActual = $('#search-telefono').val() || '';
            // Solo ejecutar si el valor es diferente al último valor procesado
            if (valorActual.trim() !== ultimoValorTelefono.trim()) {
                ultimoValorTelefono = valorActual.trim();
                console.log('Timeout ejecutado para teléfono');
                hacerBusqueda();
            }
        }, 800);
    });

    // Ejecutar búsqueda inmediata al presionar Enter
    $(document).on('keypress', '#search-nombre, #search-telefono', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            console.log('Enter presionado');
            if (timeoutNombre) clearTimeout(timeoutNombre);
            if (timeoutTelefono) clearTimeout(timeoutTelefono);
            ultimoValorNombre = $('#search-nombre').val() || '';
            ultimoValorTelefono = $('#search-telefono').val() || '';
            hacerBusqueda();
        }
    });

    // Botón limpiar filtros
    $(document).on('click', '#btn-limpiar-filtros', function(e) {
        e.preventDefault();
        limpiarFiltros();
    });

    // Verificar que los campos existan después de un pequeño delay
    setTimeout(function() {
        if ($('#search-nombre').length) {
            console.log('✓ Campo search-nombre encontrado');
        } else {
            console.log('✗ Campo search-nombre NO encontrado');
        }
        
        if ($('#search-telefono').length) {
            console.log('✓ Campo search-telefono encontrado');
        } else {
            console.log('✗ Campo search-telefono NO encontrado');
        }
    }, 100);

    // Modal de confirmación de eliminación usando Bootstrap
    $(document).on('click', '.btn-eliminar', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        if (id && nombre) {
            $('#id-eliminar').val(id);
            $('#nombre-eliminar').text(nombre);
            // Abrir modal de Bootstrap
            var modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'), {
                backdrop: false,
                keyboard: true,
                focus: true
            });
            modalEliminar.show();
        }
    });
});
