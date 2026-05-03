<?php
$layoutTitle = $layoutTitle ?? 'Mi área';
$layoutEntry = $layoutEntry ?? 'main';
$layoutMenu  = $layoutMenu  ?? [];
$content     = $content     ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/vite.php'; vite_assets($layoutEntry ?? 'main'); ?>
    <title><?php echo htmlspecialchars($layoutTitle ?? 'Mi área') ?></title>
</head>
<body>

    <?php include __DIR__ . '/header.php' ?>

    <div class="area-wrapper">

        <aside class="area-sidebar">
            <nav class="area-nav">
                <?php foreach ($layoutMenu as $item): ?>
                    <a href="<?php echo htmlspecialchars($item['href']) ?>"
                       class="area-nav__item<?php echo !empty($item['active']) ? ' area-nav__item--active' : '' ?>">
                        <i class="bi <?php echo htmlspecialchars($item['icon']) ?>"></i>
                        <?php echo htmlspecialchars($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <main class="area-content">
            <?php echo $content ?>
        </main>

    </div>

    <?php include __DIR__ . '/footer.php' ?>

</body>
</html>
