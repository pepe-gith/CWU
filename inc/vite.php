<?php
function vite_assets(string $entry = 'main', string $base = '/cwu') {
    $manifestPath = __DIR__ . '/../public/build/manifest.json';

    if (!file_exists($manifestPath)) {
        // Modo desarrollo — carga CSS directo para evitar flash, JS para HMR
        echo '<link rel="stylesheet" href="http://localhost:5173/node_modules/bootstrap/dist/css/bootstrap.min.css">' . "\n";
        echo '<link rel="stylesheet" href="http://localhost:5173/node_modules/bootstrap-icons/font/bootstrap-icons.min.css">' . "\n";
        echo '<link rel="stylesheet" href="http://localhost:5173/src/css/main.css">' . "\n";
        if ($entry !== 'main') {
            $cssFile = dirname($entry) . '/' . basename($entry, '.js') . '.css';
            if (file_exists(__DIR__ . '/../src/' . $cssFile)) {
                echo '<link rel="stylesheet" href="http://localhost:5173/src/' . $cssFile . '">' . "\n";
            }
        }
        echo '<script type="module" src="http://localhost:5173/@vite/client"></script>' . "\n";
        echo '<script type="module" src="http://localhost:5173/src/main.js"></script>' . "\n";
        if ($entry !== 'main') {
            echo '<script type="module" src="http://localhost:5173/src/' . $entry . '.js"></script>' . "\n";
        }
        return;
    }

    // Modo producción — archivos del build
    $manifest = json_decode(file_get_contents($manifestPath), true);

    // Cargar siempre el entry principal (Bootstrap, CSS global)
    $main = $manifest['src/main.js'];
    if (!empty($main['css'])) {
        foreach ($main['css'] as $css) {
            echo '<link rel="stylesheet" href="' . $base . '/public/build/' . $css . '">' . "\n";
        }
    }
    echo '<script type="module" src="' . $base . '/public/build/' . $main['file'] . '"></script>' . "\n";

    // Cargar entry específico de la página si se indicó
    if ($entry !== 'main') {
        $key = 'src/' . $entry . '.js';
        if (isset($manifest[$key])) {
            $pageEntry = $manifest[$key];
            if (!empty($pageEntry['css'])) {
                foreach ($pageEntry['css'] as $css) {
                    echo '<link rel="stylesheet" href="' . $base . '/public/build/' . $css . '">' . "\n";
                }
            }
            echo '<script type="module" src="' . $base . '/public/build/' . $pageEntry['file'] . '"></script>' . "\n";
        }
    }
}
