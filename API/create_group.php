<?php
require "../init.php";

$response = array(); // Initialize response array
$created_groups = [];
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

    $texts      =   $_POST['titleName'] ;
    $titles     =   $_POST['title'] ;
    $font_names =   $_POST['font_name'] ;
    $sizes      =   $_POST['size'] ;
    $prices     =   $_POST['price'] ;

    if ( count( $titles ) < 2 || count( $font_names ) < 2) {

        $response =array(
            'status' => 'error',
            'message' => 'At least two fonts must be selected to create a group.',
        );
        echo json_encode($response);
        exit;
    }

    try {
        $group_name = sanitize( $_POST['titleName'] );
        $key = generateMd5Key( $group_name );

        $group_id = insert_group_data( $key, $group_name );
        if( $group_id ){
            $result = insert_font_data( $group_id, $_POST );
        }

        $display_limit = 100;
        $created_groups =  make_font_group( $display_limit ) ;

        $response =array(
            'status' => 'success',
            'data' => $created_groups,
            'message' => 'Group created successfully.',
        );
    } catch (PDOException $e) {
        // Handle any errors during the database insertion
        $response =array(
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage(),
        );
    }
} else {
    $response =array(
        'status' => 'error',
        'message' => 'Invalid request method.',
    );
}

echo json_encode( $response );
?>
