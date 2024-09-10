<?php
require_once "init.php";

$fontFolder = __DIR__ . '/uploaded_font/';
$font_files = get_uploaded_file_names( $fontFolder );
//var_dump( $font_files );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="assets/css/index.css" rel="stylesheet">
    <style id="dynamic-font-styles"></style>
    <title>Font Upload Form</title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container p-2 mt-5 bg_info" >
    <div class="container mt-5">
        <div class="file-upload-area" id="fileUploadArea">
            <input type="file" id="fileInput" class="d-none">
            <i class="fas fa-upload mr-2"></i>
            <p  class="text-center m-0">Click to upload Drag & drop </p>
            <p class="text-center"> Only TTF File Allowed </p>
        </div>
        <div id="uploadStatus" class="mt-3"></div>
    </div>

    <div class="uploadedFilesHolder mt-5">
        <h2>Uploaded font lists</h2>
        <div id="font-container">
            <!-- Dynamic divs will be inserted here -->
        </div>
        <table style="width: 100%" class="uploadedFontLists" id="uploadedFontLists">
            <tr id="fontHolder">
                <th>Name</th>
                <th>Preview</th>
                <th>Action</th>
            </tr>
        </table>
    </div>

    <div class="container mt-5">
        <h2>Create Font Group</h2>
        <form id="fontGroupForm">
            <div class="form-group mr-2">
                <input type="text" class="form-control " name="titleName" required placeholder="Group title">
            </div>
            <div id="fontRows" >
                <!-- Initial Row -->
            </div>
            <div class="d-flex justify-content-between mt-3">
                <button type="button" id="addRowBtn" class="btn btn-primary">Add Row</button>
                <button type="submit" class="btn btn-success">Create Group</button>
            </div>
        </form>
        <div id="validationMessage" class="mt-3"></div>
    </div>

</div>

<script src="assets/js/index.js"></script>
</body>
</html>

<script>
    $(document).ready(function() {

    });
</script>

