<?php
/**
 * Created by PhpStorm.
 * User: Sj
 * Date: 9/12/2024
 * Time: 5:01 PM
 */

require "../init.php";

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    if( isset( $_POST['key'] )){
        $key = sanitize( $_POST['key'] );

        $display_limit = 1;
        $group_data = get_groups_data( $display_limit, $key );

        $fontFolder = __DIR__ . '../../uploaded_font/';
        $font_files = get_uploaded_file_names( $fontFolder );
//        var_test( $font_files );
        if( $group_data ){
            $response = array(
                'status' => true,
                'code' => 102,
                'data' => array(
                    'group_data' => $group_data,
                    'font_files' => $font_files,
                ),
            );
        }else{
            $response = array(
                'status' => false,
                'code' => 104,
                'data' => [],
            );
        }

    }else{
        $response = array(
            'status' => false,
            'code' => 103,
            'data' => [],
        );
    }

} else {
    $response = array(
        'status' => false,
        'code' => 105,
        'data' => [],
    );
}

echo json_encode( $response );