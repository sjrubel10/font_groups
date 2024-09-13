<?php

$fontFolder = __DIR__ . '/uploaded_font/';

$fontFiles = [];

if ( is_dir($fontFolder ) ) {
    $files = scandir($fontFolder);

    foreach ( $files as $file ) {
        if ( $file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION ) === 'ttf') {
            $fontFiles[] = $file;
        }
    }
}

echo json_encode($fontFiles);
?>
