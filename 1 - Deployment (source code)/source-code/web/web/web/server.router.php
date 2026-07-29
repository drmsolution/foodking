<?php
// Custom server router with gzip support
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Check if the file exists and serve it with gzip
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    $path = __DIR__ . '/public' . $uri;
    $ext = pathinfo($path, PATHINFO_EXTENSION);

    $gzip_types = ['js', 'css', 'html', 'svg', 'json', 'txt', 'xml'];
    $mime_map = [
        'js' => 'application/javascript',
        'css' => 'text/css',
        'html' => 'text/html',
        'svg' => 'image/svg+xml',
        'json' => 'application/json',
        'txt' => 'text/plain',
        'xml' => 'text/xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'woff2' => 'font/woff2',
        'woff' => 'font/woff',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
    ];

    $mime = $mime_map[$ext] ?? 'application/octet-stream';
    $stat = stat($path);
    $size = $stat['size'];
    $mtime = $stat['mtime'];

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . $size);
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
    header('Cache-Control: public, max-age=31536000, immutable');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

    if (in_array($ext, $gzip_types)) {
        $gz_file = $path . '.gz';
        if (file_exists($gz_file)) {
            header('Content-Encoding: gzip');
            header('Content-Length: ' . filesize($gz_file));
            readfile($gz_file);
            return true;
        }
    }

    readfile($path);
    return true;
}

// Fall back to Laravel's index.php
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/public/index.php';
require __DIR__ . '/public/index.php';
return true;
