<?php
// Define the path to the folder containing font files
$fontFolder = __DIR__ . '/uploaded_font/';

// Initialize an array to store font files
$fontFiles = [];

// Check if the folder exists
if (is_dir($fontFolder)) {
    // Get all files in the 'fonts' folder
    $files = scandir($fontFolder);

    // Loop through each file in the folder
    foreach ($files as $file) {
        // Skip the current and parent directory entries
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'ttf') {
            // Add valid TTF files to the fontFiles array
            $fontFiles[] = $file;
        }
    }
}

// Return the list of font files as a JSON response
echo json_encode($fontFiles);
?>
