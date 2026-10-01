<?php
// Application URL/path helpers. In production set APP_URL to the public HTTPS URL.
// Example: https://www.tudominio.com
function app_base_url(): string {
    $configured = trim((string)(getenv('APP_URL') ?: ''));
    if ($configured !== '') {
        return rtrim($configured, '/');
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443')
        || (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');

    if (preg_match('#^(.*?)/php/(?:auth|api|config|controllers|models)(?:/[^/]*)?$#', $script, $m)) {
        $basePath = $m[1];
    } elseif (preg_match('#^(.*?)/(?:admin|client)(?:/[^/]*)?$#', $script, $m)) {
        $basePath = $m[1];
    } else {
        $basePath = rtrim(str_replace('\\', '/', dirname($script)), '/');
        if ($basePath === '.' || $basePath === '/') $basePath = '';
    }

    return $scheme . '://' . $host . rtrim($basePath, '/');
}

function app_url(string $path = ''): string {
    return app_base_url() . '/' . ltrim($path, '/');
}
?>
