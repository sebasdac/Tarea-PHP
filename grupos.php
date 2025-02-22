<?php
require_once 'db.php';

//crear grupo
if (isset($_POST['accion']) && $_POST['accion'] == 'crear') {
    $nombre = $_POST['nombre'] ?? '';
    $sql = "INSERT INTO Grupos (Nombre) VALUES (?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$nombre]);
    $grupoId = $pdo->lastInsertId();

    //asociar tareas pendientes (opcional)
    if (!empty($_POST['tareasSeleccionadas'])) {
        foreach($_POST['tareasSeleccionadas'] as $tareaID) {
            $sql2 = "UPDATE Tareas SET GrupoID = ? WHERE TareaID = ? AND Estado = 'Pendiente'";
            $stmt2= $pdo->prepare($sql2);
            $stmt2->execute([$grupoId, $tareaID]);
        }
    }

    header('Location: grupos.php');
    exit;
}

//eliminar grupo
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM Grupos WHERE GrupoID = ?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$id]);

    //actualizar tareas que tenian este grupo
    $sql2 = "UPDATE Tareas SET GrupoID = NULL WHERE GrupoID = ?";
    $stmt2= $pdo->prepare($sql2);
    $stmt2->execute([$id]);

    header('Location: grupos.php');
    exit;
}

//listar grupos
$sql = "SELECT * FROM Grupos";
$stmt = $pdo->query($sql);
$grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

//tareas pendientes para asociar al crear un grupo
$sqlPendientes = "SELECT TareaID, Detalle FROM Tareas WHERE Estado = 'Pendiente'";
$stmt2 = $pdo->query($sqlPendientes);
$tareasPendientes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Grupos</title>
</head>
<body>
    <p><a href="index.php">Regresar a Inicio</a></p>

    <h1>Lista de Grupos</h1>
    <ul>
    <?php foreach($grupos as $grp): ?>
        <li>
            <?php echo $grp['Nombre']; ?>
            <a href="?eliminar=<?php echo $grp['GrupoID']; ?>">Eliminar</a>
            <ul>
            <?php
                $sql3 = "SELECT TareaID, Detalle FROM Tareas WHERE GrupoID = ?";
                $stmt3 = $pdo->prepare($sql3);
                $stmt3->execute([$grp['GrupoID']]);
                $tareasDeGrupo = $stmt3->fetchAll(PDO::FETCH_ASSOC);
                foreach($tareasDeGrupo as $tdg) {
                    echo "<li>".$tdg['Detalle']."</li>";
                }
            ?>
            </ul>
        </li>
    <?php endforeach; ?>
    </ul>

    <h2>Crear Grupo</h2>
    <form action="grupos.php" method="POST">
        <input type="hidden" name="accion" value="crear">
        <label>Nombre del grupo: </label>
        <input type="text" name="nombre" required>
        <h3>Asociar tareas pendientes (opcional):</h3>
        <?php foreach($tareasPendientes as $tp): ?>
            <input type="checkbox" name="tareasSeleccionadas[]" value="<?php echo $tp['TareaID']; ?>">
            <?php echo $tp['Detalle']; ?><br>
        <?php endforeach; ?>
        <button type="submit">Crear Grupo</button>
    </form>
</body>
</html>
