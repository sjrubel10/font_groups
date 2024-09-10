<?php

use Classess\FontUploader;

require_once 'Classes/FontUploader.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploader = new FontUploader($_FILES['fontFile']); // Instantiate the FontUploader class
    echo $uploader->upload(); // Call the upload method and display the result
}
