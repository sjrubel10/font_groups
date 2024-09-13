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

        if( isset( $_POST['removeFonts'] ) ){
            $remove_font_ids = $_POST['removeFonts'];
        }else{
            $remove_font_ids = [];
        }
        if( count( $remove_font_ids ) > 0 ){
            foreach ( $remove_font_ids as $id ){
                $is_remove = remove_fonts_from_group( (int)$id );
            }
        }
//        var_test( $remove_font_ids );

        parse_str($_POST['editFormData'], $editFormData);

        $is_font_updated = false;
        $is_name_change = false;
        if ( isset($editFormData['titleName'] ) ) {
            $name = $editFormData['titleName'];
            unset( $editFormData['titleName'] );
        }else{
            $name = null;
        }

        $edited_data = [];
        if( $name !== null ){

            foreach ( $name as $key => $value ){
                $is_name_change = update_group_name( $key, $name[$key] );
                $edited_data['name'] = $name[$key];
                $edited_data['key'] = $key;
            }

            $font_deatils = $editFormData;
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