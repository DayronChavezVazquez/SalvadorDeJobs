<h2>Generar comprobante Telmex</h2>

<!-- Buscador -->
<input type="text" id="folio" placeholder="Folio">
<input type="text" id="cct" placeholder="CCT">
<button id="btnBuscar">Buscar</button>

<!-- Modal Comprobante -->
<div id="modalComprobante" class="modal">
    <span class="cerrar cerrar-btn" style="cursor:pointer;">&times;</span>
    <h3>Generar Comprobante</h3>
    <form action="generar_comprobante.php" method="post">
        <input type="hidden" name="folio" id="folio_dep">
        <label>Nombre Departamento:</label>
        <input type="text" id="nombre_dep" name="nombre_departamento" readonly>
        <label>Teléfono:</label>
        <input type="text" id="telefono_dep" name="telefono" readonly>
        <hr>
        <h4>Datos del comprobante a generar</h4>
        <label>Nombre del firmante:</label>
        <input type="text" name="nombre_firmante" placeholder="Nombre completo">
        <label>Puesto del firmante:</label>
        <input type="text" name="puesto_firmante" placeholder="Puesto">
        <label>Mes:</label>
        <select name="mes" required>
            <option value="">Selecciona mes</option>
            <?php 
            $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            foreach($meses as $mes) echo "<option value='$mes'>$mes</option>";
            ?>
        </select>
        <label>Año:</label>
        <select name="anio" required>
            <option value="">Selecciona año</option>
            <?php for($a=2023; $a<=2030; $a++) echo "<option value='$a'>$a</option>"; ?>
        </select>
        <label>Cantidad a pagar:</label>
        <input type="number" name="cantidad" step="0.01" required>
        <button type="submit">Generar</button>
    </form>
</div>
