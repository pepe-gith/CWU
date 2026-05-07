<?php
function vite_assets(string $entrada = 'main', string $rutadBase = '/cwu') {
    // Ruta al manifest generado por Vite para producción
    $manifestPath = __DIR__ . '/../public/build/manifest.json';

    if (!file_exists($manifestPath)) {
        // Modo desarrollo carga CSS directomente desde Vite
        echo '<link rel="stylesheet" href="http://localhost:5173/node_modules/bootstrap/dist/css/bootstrap.min.css">' . "\n";
        echo '<link rel="stylesheet" href="http://localhost:5173/node_modules/bootstrap-icons/font/bootstrap-icons.min.css">' . "\n";
        echo '<link rel="stylesheet" href="http://localhost:5173/src/css/main.css">' . "\n";
        if ($entrada !== 'main') {
            $cssFile = dirname($entrada) . '/' . basename($entrada, '.js') . '.css';
            if (file_exists(__DIR__ . '/../src/' . $cssFile)) {
                echo '<link rel="stylesheet" href="http://localhost:5173/src/' . $cssFile . '">' . "\n";
            }
        }
        echo '<script type="module" src="http://localhost:5173/@vite/client"></script>' . "\n";
        echo '<script type="module" src="http://localhost:5173/src/main.js"></script>' . "\n";
        if ($entrada !== 'main') {
            echo '<script type="module" src="http://localhost:5173/src/' . $entrada . '.js"></script>' . "\n";
        }
        return;
    }

    // Modo producción
    $manifest = json_decode(file_get_contents($manifestPath), true);

    // Cargar siempre la entrada principal (Bootstrap, CSS global)
    $main = $manifest['src/main.js'];
    if (!empty($main['css'])) {
        foreach ($main['css'] as $css) {
            echo '<link rel="stylesheet" href="' . $rutadBase . '/public/build/' . $css . '">' . "\n";
        }
    }
    echo '<script type="module" src="' . $rutadBase . '/public/build/' . $main['file'] . '"></script>' . "\n";

    // Cargar entrada específico de la página si se indicó
    if ($entrada !== 'main') {
        $key = 'src/' . $entrada . '.js';
        if (isset($manifest[$key])) {
            $pageentrada = $manifest[$key];
            if (!empty($pageentrada['css'])) {
                foreach ($pageentrada['css'] as $css) {
                    echo '<link rel="stylesheet" href="' . $rutadBase . '/public/build/' . $css . '">' . "\n";
                }
            }
            echo '<script type="module" src="' . $rutadBase . '/public/build/' . $pageentrada['file'] . '"></script>' . "\n";
        }
    }
}
