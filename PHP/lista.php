<?php
$archivo = 'datos.xml';


if (!file_exists($archivo)) {
    $nuevo = new SimpleXMLElement('<personas></personas>');
    $nuevo->asXML($archivo);
}

$xml = simplexml_load_file($archivo);

// ELIMINAR PERSONA
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    unset($xml->persona[$id]);
    $xml->asXML($archivo);
    header("Location: lista.php?mensaje=" . urlencode("✅ El usuario ha sido eliminado exitosamente."));
    exit;
}

// EDITAR PERSONA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_id'])) {
    $id = intval($_POST['editar_id']);
    if (isset($xml->persona[$id])) {
        $xml->persona[$id]->nombre = $_POST['nombre'];
        $xml->persona[$id]->apellido = $_POST['apellido'];
        $xml->persona[$id]->correo = $_POST['correo'];
        $xml->persona[$id]->telefono = $_POST['telefono'];
        $xml->persona[$id]->fecha_nacimiento = $_POST['fecha_nacimiento'];
        $xml->asXML($archivo);
        header("Location: lista.php?mensaje=" . urlencode("✅ Datos actualizados correctamente."));
        exit;
    } else {
        header("Location: lista.php?mensaje=" . urlencode("⚠️ No se encontró el registro a editar."));
        exit;
    }
}

// ORDENAR
$ordenarPor = $_GET['ordenar'] ?? 'nombre';
$direccion = $_GET['dir'] ?? 'asc';

// Cargar personas con su índice original
$personas = [];
foreach ($xml->persona as $index => $p) {
    $personas[] = [
        'id' => $index,
        'nombre' => (string)$p->nombre,
        'apellido' => (string)$p->apellido,
        'correo' => (string)$p->correo,
        'telefono'=> (string)$p->telefono,
        'fecha' => (string)$p->fecha_nacimiento
    ];
}

// Ordenar sin perder el id real
usort($personas, function ($a, $b) use ($ordenarPor, $direccion) {
    return ($direccion === 'asc')
        ? strcmp($a[$ordenarPor], $b[$ordenarPor])
        : strcmp($b[$ordenarPor], $a[$ordenarPor]);
});

$mensaje = $_GET['mensaje'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Listado de Personas</title>
  <link rel="stylesheet" href="../CSS/style.css">

</head>
<body>
  <div class="container">
    <h1>Listado de Personas</h1>

    <?php if ($mensaje): ?>
      <div class="alerta"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="get" class="filter-form">
      <label>Ordenar por:</label>
      <select name="ordenar">
        <option value="nombre" <?= $ordenarPor == 'nombre' ? 'selected' : '' ?>>Nombre</option>
        <option value="apellido" <?= $ordenarPor == 'apellido' ? 'selected' : '' ?>>Apellido</option>
        <option value="correo" <?= $ordenarPor == 'correo' ? 'selected' : '' ?>>Correo</option>
        <option value="telefono" <?= $ordenarPor == 'telefono' ? 'selected' : '' ?>>Teléfono</option>
        <option value="fecha" <?= $ordenarPor == 'fecha' ? 'selected' : '' ?>>Fecha de nacimiento</option>
      </select>

      <select name="dir">
        <option value="asc" <?= $direccion == 'asc' ? 'selected' : '' ?>>Ascendente</option>
        <option value="desc" <?= $direccion == 'desc' ? 'selected' : '' ?>>Descendente</option>
      </select>

      <button type="submit">Aplicar</button>
    </form>

    <table>
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Correo</th>
        <th>Teléfono</th>
        <th>Fecha Nac.</th>
        <th>Acciones</th>
      </tr>

      <?php foreach ($personas as $i => $p): ?>
      <tr id="fila<?= $i ?>">
        <form method="post">
          <td><?= $i + 1 ?></td>
          <td><input type="text" name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>" readonly></td>
          <td><input type="text" name="apellido" value="<?= htmlspecialchars($p['apellido']) ?>" readonly></td>
          <td><input type="email" name="correo" value="<?= htmlspecialchars($p['correo']) ?>" readonly></td>
          <td><input type="text" name="telefono" value="<?= htmlspecialchars($p['telefono']) ?>" readonly></td>
          <td><input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($p['fecha']) ?>" readonly></td>
          <td>
            <input type="hidden" name="editar_id" value="<?= $p['id'] ?>">
            <button type="button" class="editar" onclick="habilitarEdicion(<?= $i ?>)">Editar</button>
            <button type="submit" class="guardar" style="display:none;">Guardar</button>
            <a class="button delete" href="?eliminar=<?= $p['id'] ?>" onclick="return confirm('¿Seguro que quieres eliminar a <?= htmlspecialchars($p['nombre']) ?>?');">Eliminar</a>
          </td>
        </form>
      </tr>
      <?php endforeach; ?>
    </table>

    <br>
    <a class="button" href="../HTML/inicio.html">🏠 Volver al inicio</a>
    <button class="btn-volver" onclick="window.history.back()">⬅️ Atrás</button>
  </div>

  <script>
    function habilitarEdicion(id) {
      const fila = document.querySelector(`#fila${id}`);
      if (!fila) return;

      const inputs = fila.querySelectorAll('input[type=text], input[type=email], input[type=date]');
      inputs.forEach(input => {
        input.removeAttribute('readonly');
        input.style.backgroundColor = '#fff';
        input.style.border = '1px solid #ccc';
      });

      fila.querySelector('.editar').style.display = 'none';
      fila.querySelector('.guardar').style.display = 'inline-block';
    }
  </script>
</body>
</html>