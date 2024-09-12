<?php
/**
 * Created by PhpStorm.
 * User: Sj
 * Date: 9/12/2024
 * Time: 2:45 PM
 */

require "../init.php";

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    if( isset( $_POST['key'] )){
        $key = sanitize( $_POST['key'] );

        $result = updateGroupRecorded( $key, 0 );
        if( $result ){
            $response = array(
                'status' => true,
                'code' => 102,
                'message' => 'Successfully Deleted',
            );
        }else{
            $response = array(
                'status' => false,
                'code' => 102,
                'message' => 'Failed in deletion',
            );
        }

    }else{
        $response = array(
            'status' => false,
            'code' => 102,
            'message' => 'Font group in not set',
        );
    }

} else {
    $response = array(
        'status' => false,
        'code' => 105,
        'message' => 'Server Error',
    );
}

echo json_encode( $response );