<?php
/**
 * Created by PhpStorm.
 * User: Sj
 * Date: 9/12/2024
 * Time: 3:58 PM
 */

$fontFolder = __DIR__ . '../../uploaded_font/';

$fontFiles = [];

if ( is_dir( $fontFolder ) ) {
    $files = scandir( $fontFolder );

    // Loop through each file in the folder
    foreach ( $files as $file ) {
        // Skip the current and parent directory entries
        if ( $file !== '.' && $file !== '..' && pathinfo( $file, PATHINFO_EXTENSION ) === 'ttf') {
            // Add valid TTF files to the fontFiles array
            $fontFiles[] = $file;
        }
    }
}

echo json_encode( $fontFiles );
?>
