<?php
require_once 'db.php';

//crear encargado
if (isset($_POST['accion']) && $_POST['accion'] == 'crear') {
    $nombre = $_POST['nombre'] ?? '';
    $sql = "INSERT INTO Encargados (Nombre) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre]);
    header('Location: encargados.php');
    exit;
}

//editar encargado
if (isset($_POST['accion']) && $_POST['accion'] == 'editar') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'] ?? '';
    $sql = "UPDATE Encargados SET Nombre = ? WHERE EncargadoID = ?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$nombre, $id]);
    header('Location: encargados.php');
    exit;
}

//eliminar encargado
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM Encargados WHERE EncargadoID = ?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$id]);
    //dejar las tareas sin encargado
    $sql2 = "UPDATE Tareas SET EncargadoID = NULL WHERE EncargadoID = ?";
    $stmt2= $pdo->prepare($sql2);
    $stmt2->execute([$id]);
    header('Location: encargados.php');
    exit;
}

//listar encargado
$sql = "SELECT * FROM Encargados";
$stmt = $pdo->query($sql);
$encargados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Encargados</title>
</head>
<body>
    <p><a href="index.php">Regresar a Inicio</a></p>
    
    <h1>Lista de Encargados</h1>
    <ul>
    <?php foreach($encargados as $enc): ?>
        <li>
            <?php echo $enc['Nombre']; ?>
            <a href="?eliminar=<?php echo $enc['EncargadoID']; ?>">Eliminar</a>
            <form action="encargados.php" method="POST" style="display:inline;">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?php echo $enc['EncargadoID']; ?>">
                <input type="text" name="nombre" value="<?php echo $enc['Nombre']; ?>">
                <button type="submit">Guardar</button>
            </form>
        </li>
    <?php endforeach; ?>
    </ul>

    <h2>Crear Encargado</h2>
    <form action="encargados.php" method="POST">
        <input type="hidden" name="accion" value="crear">
        <label>Nombre: </label>
        <input type="text" name="nombre" required>
        <button type="submit">Crear</button>
    </form>
</body>
</html>
