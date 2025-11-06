<?php
// consultar.php
// Esta página muestra todas las escuelas con paginación y botón para agregar

$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$por_pagina = 20;
$inicio = ($pagina > 1) ? ($pagina * $por_pagina - $por_pagina) : 0;
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$q_tel = isset($_GET['q_tel']) ? trim($_GET['q_tel']) : '';

// Contamos el total de registros con filtros opcionales
$where = [];
$params = [];
if ($q !== '') {
    $where[] = "nombre_departamento LIKE :q";
    $params[':q'] = "%$q%";
}
if ($q_tel !== '') {
    $where[] = "telefono LIKE :q_tel";
    $params[':q_tel'] = "%$q_tel%";
}
$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM ct_departamentos $whereClause");
$stmtCount->execute($params);
$total = (int)$stmtCount->fetchColumn();
$total_paginas = ceil($total / $por_pagina);

// Obtenemos los registros de la página actual (con filtros y paginación)
$stmt = $conn->prepare("SELECT * FROM ct_departamentos $whereClause LIMIT " . (int)$inicio . ", " . (int)$por_pagina);
$stmt->execute($params);
$departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Escuelas / Departamentos</h2>
    <a class="btn btn-primary" href="?page=agregar">Agregar nueva escuela</a>
</div>

<!-- Filtros de búsqueda a la izquierda -->
<div class="filters-container mb-4">
    <div class="filter-group">
        <label class="form-label">Buscar por nombre:</label>
        <input type="text" id="search-nombre" class="form-control search-input autocomplete" name="q" value="<?= htmlspecialchars((string)$q, ENT_QUOTES, 'UTF-8') ?>" placeholder="Escribe el nombre..." autocomplete="off">
        <div id="autocomplete-nombre" class="autocomplete-results"></div>
    </div>
    <div class="filter-group">
        <label class="form-label">Buscar por teléfono:</label>
        <input type="text" id="search-telefono" class="form-control search-input autocomplete" name="q_tel" value="<?= htmlspecialchars((string)$q_tel, ENT_QUOTES, 'UTF-8') ?>" placeholder="Escribe el teléfono..." autocomplete="off">
        <div id="autocomplete-telefono" class="autocomplete-results"></div>
    </div>
    <?php if ($q !== '' || $q_tel !== ''): ?>
        <a href="?page=consultar" class="btn btn-outline-secondary" id="btn-limpiar-filtros">Limpiar filtros</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Folio</th>
                        <th>Folio Interno</th>
                        <th>Encargado</th>
                        <th>Cargo</th>
                        <th>Domicilio</th>
                        <th>CCT</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $numero = $inicio + 1; // Iniciar numeración desde el primer registro de la página
                    foreach ($departamentos as $dep): ?>
                        <tr>
                            <td><?= $numero++ ?></td>
                            <td><?= $dep['nombre_departamento'] ?></td>
                            <td><?= $dep['telefono'] ?></td>
                            <td><?= $dep['folio'] ?></td>
                            <td><?= $dep['folio_interno'] ?></td>
                            <td><?= $dep['nombre_encargado'] ?></td>
                            <td><?= $dep['cargo'] ?></td>
                            <td><?= $dep['domicilio'] ?></td>
                            <td><?= $dep['cct'] ?></td>
                            <td class="actions">
                                <a class="icon-btn" title="Editar" href="?page=editar&id=<?= $dep['id_departamento'] ?>" aria-label="Editar">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" fill="#4b5563" />
                                        <path d="M20.71 7.04a1.003 1.003 0 000-1.42l-2.34-2.34a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" fill="#4b5563" />
                                    </svg>
                                </a>
                                <button type="button" class="icon-btn btn-eliminar" title="Eliminar" aria-label="Eliminar" data-id="<?= $dep['id_departamento'] ?>" data-nombre="<?= htmlspecialchars((string)($dep['nombre_departamento'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 7h12" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" />
                                        <path d="M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" />
                                        <path d="M7 7l1 13a2 2 0 002 2h4a2 2 0 002-2l1-13" stroke="#b91c1c" stroke-width="2" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Paginación -->
<nav aria-label="Paginación" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                <a class="page-link" href="?page=consultar&p=<?= $i ?><?= $q !== '' ? '&q=' . urlencode($q) : '' ?><?= $q_tel !== '' ? ($q !== '' ? '&' : '') . 'q_tel=' . urlencode($q_tel) : '' ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarLabel">⚠️ Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar esta institución?</p>
                <p class="fw-bold mb-3">Institución: <span id="nombre-eliminar"></span></p>
                <p class="text-danger small">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <form id="form-eliminar" action="eliminar_departamento.php" method="post" style="display:inline;">
                    <input type="hidden" name="id" id="id-eliminar">
                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelar-eliminar">Cancelar</button>
            </div>
        </div>
    </div>
</div>