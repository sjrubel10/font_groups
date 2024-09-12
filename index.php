<?php
require_once "init.php";

$fontFolder = __DIR__ . '/uploaded_font/';
$font_files = get_uploaded_file_names( $fontFolder );

$display_limit = 100;
$created_groups =  make_font_group( $display_limit ) ;

//$group_data = get_groups_data( 1, '85f29283b25e' );
//var_test( $group_data );
//make_font_group();

//var_test( $font_files );

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
<h1 class="text-center">Create Font Group</h1>
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
            <div id="fontRows" class="fontRows">
                <!-- Initial Row -->
            </div>
            <div class="d-flex justify-content-between mt-3">
                <button type="button" id="addRowBtn" class="btn btn-primary">Add Row</button>
                <button type="submit" class="btn btn-success">Create Group</button>
            </div>
        </form>
        <div id="validationMessage" class="mt-3"></div>
    </div>


    <div class="container mt-4">
        <h2 class="mb-4">Group Data Display</h2>

        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
            <tr>
                <th>Name</th>
                <th>Font Names</th>
                <th>Counts</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody id="loadGroups">
            <?php if( count( $created_groups ) > 0 ){ ?>
                <?php foreach ($created_groups as $group) : ?>
                    <tr id="<?php echo htmlspecialchars( $group["key"] )?>">
                        <td><?php echo htmlspecialchars($group["name"]); ?></td>
                        <td><?php echo htmlspecialchars($group["font_name"]); ?></td>
                        <td><?php echo htmlspecialchars($group["counts"]); ?></td>
                        <td><span class="editFontGroup" id="edit-<?php echo htmlspecialchars( $group["key"] )?>">Edit</span> <span class="deleteFontGroup" id="delete-<?php echo htmlspecialchars( $group["key"] )?>">Delete</span></td>
                    </tr>
                <?php endforeach; ?>
            <?php } else {?>
                <tr>
                    <td>No Created Form Groups Found!</td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>




</div>

<script src="assets/js/index.js"></script>
</body>
</html>

<script>
    $(document).ready(function() {

    });
</script>

