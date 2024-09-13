<?php
if ( isset($_POST['filename'] ) ) {
    $fileName = $_POST['filename'];
    $filePath = '../uploaded_font/' . $fileName; // Define the file path

    // Check if the file exists and delete it
    if ( file_exists($filePath ) ) {
        if ( unlink($filePath ) ) {
            echo "File deleted successfully.";
        } else {
            echo "Error deleting the file.";
        }
    } else {
        echo "File does not exist.";
    }
} else {
    echo "No file specified.";
}
?>

