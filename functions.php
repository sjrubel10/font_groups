<?php

function get_uploaded_file_names( $fontFolder ){
    $fontFiles = [];

// Check if the folder exists
    if ( is_dir( $fontFolder ) ) {
        // Get all files in the 'fonts' folder
        $files = scandir( $fontFolder );

        // Loop through each file in the folder
        foreach ($files as $file) {
            // Skip the current and parent directory entries
            if ($file !== '.' && $file !== '..' && pathinfo( $file, PATHINFO_EXTENSION) === 'ttf' ) {
                $fontFiles[] = $file;
            }
        }
    }

    return $fontFiles;
}
