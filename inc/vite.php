<?php
function vite_assets(string $base = '/cwu') {
    $manifestPath = __DIR__ . '/../public/build/manifest.json';

    if (!file_exists($manifestPath)) {
        // Modo desarrollo — Vite dev server
        echo '<script type="module" src="http://localhost:5173/@vite/client"></script>' . "\n";
        echo '<script type="module" src="http://localhost:5173/src/main.js"></script>' . "\n";
        return;
    }

    // Modo producción — archivos del build
    $manifest = json_decode(file_get_contents($manifestPath), true);
    $entry = $manifest['src/main.js'];

    if (!empty($entry['css'])) {
        foreach ($entry['css'] as $css) {
            echo '<link rel="stylesheet" href="' . $base . '/public/build/' . $css . '">' . "\n";
        }
    }
    echo '<script type="module" src="' . $base . '/public/build/' . $entry['file'] . '"></script>' . "\n";
}
