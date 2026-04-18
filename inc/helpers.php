<?php

function responderError(int $code, string $mensaje): never {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $mensaje]);
    exit;
}
