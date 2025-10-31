<?php
$archivo = 'datos.xml';
$mensaje = "";

// Crear el XML si no existe
if (!file_exists($archivo)) {
    $xml = new SimpleXMLElement('<personas></personas>');
    $xml->asXML($archivo);
}

// 🧩 AGREGAR PERSONA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xml = simplexml_load_file($archivo);

    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $fecha = trim($_POST['fecha_nacimiento']);

    if ($nombre !== '' && $correo !== '' && $fecha !== '') {
        $nueva = $xml->addChild('persona');
        $nueva->addChild('nombre', $nombre);
        $nueva->addChild('apellido', $apellido);
        $nueva->addChild('correo', $correo);
        $nueva->addChild('telefono', $telefono);
        $nueva->addChild('fecha_nacimiento', $fecha);
        $xml->asXML($archivo);

        // Guardar mensaje para mostrar en la misma página
        $mensaje = "✅ $nombre $apellido ha sido agregada exitosamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Persona</title>
  <link rel="stylesheet" href="../CSS/style.css">
  <style>
    .mensaje-exito {
      background-color: #d4edda;
      color: #155724;
      padding: 10px 20px;
      border: 1px solid #c3e6cb;
      border-radius: 8px;
      margin-bottom: 15px;
      font-weight: 500;
    }
    .btn-volver {
      background-color: #ccc;
      padding: 8px 15px;
      border-radius: 5px;
      text-decoration: none;
      border: none;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Agregar nueva persona</h1>

    <?php if (!empty($mensaje)): ?>
      <div class="mensaje-exito"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="post">
      <label>Nombre:</label>
      <input type="text" name="nombre" required>

      <label>Apellido:</label>
      <input type="text" name="apellido" required>

      <label>Correo:</label>
      <input type="email" name="correo" required>

      <label>Teléfono:</label>
      <input type="number" name="telefono" required>

      <label>Fecha de nacimiento:</label>
      <input type="date" name="fecha_nacimiento" required>

      <button type="submit" name="agregar">Agregar</button>
    </form>

    <br>
    <a class="button" href="../PHP/lista.php">Ver lista de personas</a>
    <button class="btn-volver" onclick="window.history.back()">⬅️ Atrás</button>
  </div>

  <!-- Script opcional para ocultar el mensaje automáticamente -->
  <script>
    const mensaje = document.querySelector('.mensaje-exito');
    if (mensaje) {
      setTimeout(() => mensaje.style.display = 'none', 4000);
    }
  </script>
</body>
</html>



