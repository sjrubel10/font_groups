<?php
require "init.php";

$response = array(); // Initialize response array
$created_groups = [];
// Check if the request method is POST
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

//    var_dump( $_POST );
    // Get the form data
    $texts = $_POST['titleName']; // Array of text inputs
    $titles = $_POST['title']; // Array of font selections
    $font_names = $_POST['font_name']; // Array of font selections
    $sizes = $_POST['size']; // Array of number1 inputs
    $prices= $_POST['price']; // Array of number2 inputs

    // Validate input
    if (count($titles) < 2 || count($font_names) < 2) {
        $response['status'] = 'error';
        $response['message'] = 'At least two fonts must be selected to create a group.';
        echo json_encode($response);
        exit;
    }

    // Prepare and execute database insertion
    try {
        $group_name = sanitize( $_POST['titleName'] );
        $key = generateMd5Key( $group_name );

        $group_id = insert_group_data( $key, $group_name );
        if( $group_id ){
            $result = insert_font_data( $group_id, $_POST );
        }

        $display_limit = 100;
        $created_groups =  make_font_group( $display_limit ) ;
        // If the insertion is successful, send a success response
        $response['status'] = 'success';
        $response['data'] = $created_groups;
        $response['message'] = 'Group created successfully.';
    } catch (PDOException $e) {
        // Handle any errors during the database insertion
        $response['status'] = 'error';
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    // If the request method is not POST, return an error
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

// Return the response as JSON
echo json_encode( $response );
?>
