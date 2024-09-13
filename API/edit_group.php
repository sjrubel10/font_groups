<?php
/**
 * Created by PhpStorm.
 * User: Sj
 * Date: 9/12/2024
 * Time: 11:26 PM
 */

require "../init.php";

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    if( isset( $_POST )){

        $is_font_updated = false;
        $is_name_change = false;
        if ( isset($_POST['titleName'] ) ) {
            $name = $_POST['titleName'];
            unset( $_POST['titleName'] );
        }else{
            $name = null;
        }

        $edited_data = [];
        if( $name !== null ){

            foreach ( $name as $key => $value ){
                $is_name_change = updateGroupName( $key, $name[$key] );
                $edited_data['name'] = $name[$key];
                $edited_data['key'] = $key;
            }

            $font_deatils = $_POST;
            $fonts = '';
            foreach ( $font_deatils as $key => $font_detail ){
                $is_font_updated = updateFont( $key, $font_detail['title'], $font_detail['font_name'], (int)$font_detail['size'], (int)$font_detail['price'] );
                $fonts .= $font_detail['font_name'].', ';
            }

            $fonts = rtrim( $fonts, ", " );
            $edited_data['font_name'] = $fonts;
            $edited_data['counts'] = count( $font_deatils );

        }else{
            $font_details = [];
        }

        if( $is_font_updated ){
            $response = array(
                'status' => true,
                'data' => $edited_data,
                'code' => 102,
                'message' => 'Successfully Updated',
            );
        }else{
            $response = array(
                'status' => false,
                'code' => 102,
                'message' => ' update Failed ',
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