<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../../inc/vite.php'; vite_assets(); ?>
    <title>Inicio Cliente</title>
</head>
<body>

    <?php include('../../inc/header.php') ?>

    <main>
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['cliente']['nombre'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?></h1>
    </main>

    <?php include('../../inc/footer.php') ?>

</body>
</html>
