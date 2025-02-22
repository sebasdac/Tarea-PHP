<?php
require_once 'db.php';

//crear tarea
if (isset($_POST['accion']) && $_POST['accion'] == 'crear') {
    $detalle = $_POST['detalle'] ?? '';
    $encargadoID = $_POST['encargadoID'] ?? null;
    $grupoID = $_POST['grupoID'] ?? null;
    if ($encargadoID === '') $encargadoID = null;
    if ($grupoID === '') $grupoID = null;

    $sql = "INSERT INTO Tareas (Detalle, Estado, FechaFinalizacion, EncargadoID, GrupoID)
            VALUES (?, 'Pendiente', NULL, ?, ?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$detalle, $encargadoID, $grupoID]);

    header('Location: tareas.php');
    exit;
}

//editar tarea
if (isset($_POST['accion']) && $_POST['accion'] == 'editar') {
    $tareaID = $_POST['tareaID'];
    $detalle = $_POST['detalle'] ?? '';
    $encargadoID = $_POST['encargadoID'] ?? null;
    $grupoID = $_POST['grupoID'] ?? null;
    if ($encargadoID === '') $encargadoID = null;
    if ($grupoID === '') $grupoID = null;

    $sql = "UPDATE Tareas
            SET Detalle = ?, EncargadoID = ?, GrupoID = ?
            WHERE TareaID = ?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$detalle, $encargadoID, $grupoID, $tareaID]);

    header('Location: tareas.php');
    exit;
}

//eliminar tarea
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM Tareas WHERE TareaID = ?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$id]);

    header('Location: tareas.php');
    exit;
}

//marcar como finalizada
if (isset($_GET['finalizar'])) {
    $id = $_GET['finalizar'];
    $fecha = date('Y-m-d H:i:s');
    $sql = "UPDATE Tareas
            SET Estado = 'Finalizada',
                FechaFinalizacion = ?
            WHERE TareaID = ?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$fecha, $id]);

    header('Location: tareas.php');
    exit;
}

//reactivar tarea
if (isset($_GET['reactivar'])) {
    $id = $_GET['reactivar'];
    $sql = "UPDATE Tareas
            SET Estado = 'Pendiente',
                FechaFinalizacion = NULL
            WHERE TareaID = ?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$id]);

    header('Location: tareas.php');
    exit;
}

//listar tareas (pendientes y finalizadas)
$sqlPendientes = "SELECT T.*, E.Nombre as EncargadoNombre, G.Nombre as GrupoNombre
                  FROM Tareas T
                  LEFT JOIN Encargados E ON T.EncargadoID = E.EncargadoID
                  LEFT JOIN Grupos G ON T.GrupoID = G.GrupoID
                  WHERE T.Estado = 'Pendiente'
                  ORDER BY T.TareaID DESC";
$stmt = $pdo->query($sqlPendientes);
$tareasPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlFinalizadas = "SELECT T.*, E.Nombre as EncargadoNombre, G.Nombre as GrupoNombre
                   FROM Tareas T
                   LEFT JOIN Encargados E ON T.EncargadoID = E.EncargadoID
                   LEFT JOIN Grupos G ON T.GrupoID = G.GrupoID
                   WHERE T.Estado = 'Finalizada'
                   ORDER BY T.TareaID DESC";
$stmt2 = $pdo->query($sqlFinalizadas);
$tareasFinalizadas = $stmt2->fetchAll(PDO::FETCH_ASSOC);

//listar encargados y grupos para los formularios
$sqlEnc = "SELECT * FROM Encargados";
$stmtEnc = $pdo->query($sqlEnc);
$encargados = $stmtEnc->fetchAll(PDO::FETCH_ASSOC);

$sqlGrp = "SELECT * FROM Grupos";
$stmtGrp = $pdo->query($sqlGrp);
$grupos = $stmtGrp->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tareas</title>
</head>
<body>
    <p><a href="index.php">Regresar a Inicio</a></p>

    <h1>Tareas Pendientes</h1>
    <ul>
    <?php foreach($tareasPendientes as $tp): ?>
        <li>
            <strong><?php echo $tp['Detalle']; ?></strong> -
            <?php
                echo $tp['EncargadoNombre'] ? $tp['EncargadoNombre'] : "Sin encargado asignado";
                echo " - ";
                echo $tp['GrupoNombre'] ? $tp['GrupoNombre'] : "Sin grupo asignado";
            ?>
            <a href="?finalizar=<?php echo $tp['TareaID']; ?>">Finalizar</a>
            <a href="?eliminar=<?php echo $tp['TareaID']; ?>">Eliminar</a>

            <form action="tareas.php" method="POST" style="margin-top:5px;">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="tareaID" value="<?php echo $tp['TareaID']; ?>">
                <input type="text" name="detalle" value="<?php echo $tp['Detalle']; ?>">
                
                <select name="encargadoID">
                    <option value="">Sin encargado</option>
                    <?php foreach($encargados as $enc): ?>
                        <option value="<?php echo $enc['EncargadoID']; ?>"
                            <?php if ($enc['EncargadoID'] == $tp['EncargadoID']) echo 'selected'; ?>>
                            <?php echo $enc['Nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="grupoID">
                    <option value="">Sin grupo</option>
                    <?php foreach($grupos as $g): ?>
                        <option value="<?php echo $g['GrupoID']; ?>"
                            <?php if ($g['GrupoID'] == $tp['GrupoID']) echo 'selected'; ?>>
                            <?php echo $g['Nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Guardar</button>
            </form>
        </li>
    <?php endforeach; ?>
    </ul>

    <h1>Tareas Finalizadas</h1>
    <ul>
    <?php foreach($tareasFinalizadas as $tf): ?>
        <li style="text-decoration: line-through;">
            <?php echo $tf['Detalle']; ?> -
            <?php
                echo $tf['EncargadoNombre'] ? $tf['EncargadoNombre'] : "Sin encargado asignado";
                echo " - ";
                echo $tf['GrupoNombre'] ? $tf['GrupoNombre'] : "Sin grupo asignado";
            ?>
            <small>(Finalizada el <?php echo $tf['FechaFinalizacion']; ?>)</small>
            <a href="?reactivar=<?php echo $tf['TareaID']; ?>">Reactivar</a>
            <a href="?eliminar=<?php echo $tf['TareaID']; ?>">Eliminar</a>
        </li>
    <?php endforeach; ?>
    </ul>

    <h2>Crear Tarea</h2>
    <form action="tareas.php" method="POST">
        <input type="hidden" name="accion" value="crear">
        <label>Detalle: </label>
        <input type="text" name="detalle" required>
        
        <label>Encargado: </label>
        <select name="encargadoID">
            <option value="">Sin encargado</option>
            <?php foreach($encargados as $enc): ?>
                <option value="<?php echo $enc['EncargadoID']; ?>">
                    <?php echo $enc['Nombre']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Grupo: </label>
        <select name="grupoID">
            <option value="">Sin grupo</option>
            <?php foreach($grupos as $g): ?>
                <option value="<?php echo $g['GrupoID']; ?>">
                    <?php echo $g['Nombre']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit">Crear</button>
    </form>
</body>
</html>
