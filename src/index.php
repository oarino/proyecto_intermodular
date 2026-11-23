<?php
require __DIR__ . '/db.php';

$version = getenv('APP_VERSION') ?: 'local';
$fecha   = date('Y-m-d H:i:s');

try {
    $pdo = getConnection();
    $stmt = $pdo->query('SELECT * FROM alumnos_demo ORDER BY id');
    $alumnos = $stmt->fetchAll();
    $dbStatus = 'conectada correctamente';
} catch (Throwable $e) {
    $alumnos = [];
    $dbStatus = 'error de conexión: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Proyecto Intermodular · Demo CI/CD</title>
<style>
  body { font-family: system-ui, sans-serif; max-width: 720px; margin: 40px auto; padding: 0 20px; color: #1b2733; }
  .badge { display:inline-block; padding:4px 10px; border-radius:6px; background:#0b2545; color:#fff; font-size:13px; font-family: monospace; }
  table { border-collapse: collapse; width:100%; margin-top:16px; }
  th, td { border:1px solid #dce6ec; padding:8px 10px; text-align:left; }
  th { background:#e9f1f6; }
  .status { color: #1c7293; }
</style>
</head>
<body>
  <h1>Proyecto Intermodular</h1>
  <p><span class="badge">versión desplegada: <?= htmlspecialchars($version) ?></span></p>
  <p>Servidor respondiendo el <strong><?= $fecha ?></strong>.<br>
     Estado de la base de datos: <span class="status"><?= htmlspecialchars($dbStatus) ?></span>.</p>

  <table>
    <tr><th>ID</th><th>Nombre</th><th>Grupo</th></tr>
    <?php foreach ($alumnos as $a): ?>
    <tr>
      <td><?= htmlspecialchars((string) $a['id']) ?></td>
      <td><?= htmlspecialchars($a['nombre']) ?></td>
      <td><?= htmlspecialchars($a['grupo']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <p style="margin-top:32px; font-size:13px; color:#5c6b77;">
    Cada vez que el pipeline de CI/CD redespliega la aplicación, la "versión desplegada"
    de arriba cambia al hash del commit correspondiente: es la forma más sencilla de
    comprobar en clase que el despliegue automático ha funcionado de verdad.
  </p>

 

</body>
</html>
