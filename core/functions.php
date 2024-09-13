<?php

function get_uploaded_file_names( $fontFolder )
{
    $fontFiles = [];

    if ( is_dir( $fontFolder ) ) {
        $files = scandir( $fontFolder );

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && pathinfo( $file, PATHINFO_EXTENSION) === 'ttf' ) {
                $fontFiles[] = $file;
            }
        }
    }

    return $fontFiles;
}

function generateMd5Key( $groupName )
{
    $md5Hash = md5($groupName);
    $shortKey = substr($md5Hash, 0, 12 );

    return $shortKey;
}

function insert_group_data( $key, $name )
{
    $db = new Database();
    $stmt = $db->conn->prepare("INSERT INTO groups (`key`, `name`) VALUES ( ?, ? )");
    $stmt->bind_param("ss", $key, $name);

    if ($stmt->execute()) {
        $last_id = $db->conn->insert_id;
        $stmt->close();

        return $last_id;
    } else {
        $stmt->close();

        return false;
    }
}

function insert_font_data( $group_id, $data )
{
    $db = new Database();

    if ($db->conn->connect_error) {
        die("Connection failed: " . $db->conn->connect_error);
    }

    $stmt = $db->conn->prepare("INSERT INTO `fonts` (`group_id`, `title`, `font_name`, `size`, `price`) VALUES (?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Error preparing the statement: " . $db->conn->error);
    }

    $result = 1;
    for ($i = 0; $i < count($data['title']); $i++) {
        $title = sanitize($data['title'][$i]);
        $fontName = sanitize($data['font_name'][$i]);
        $size = sanitize($data['size'][$i]);
        $price = sanitize($data['price'][$i]);

        $stmt->bind_param("issii", $group_id, $title, $fontName, $size, $price);

        if (!$stmt->execute()) {
            $result = 0;
            break;
        }
    }

    $stmt->close();
    $db->close_connection();

    return $result;
}



function get_groups_data( $display_limit, $group_key= null )
{
    $db = new Database();
    $font_details = array();
    $id = $key = $name = $created_date = $recorded = null;

    if( $group_key === null ){
        $query = "SELECT `id`, `key`, `name`, `created_date`, `recorded` FROM `groups` WHERE `recorded` = 1  ORDER BY `id` DESC LIMIT ?";
    }else{
        $query = "SELECT `id`, `key`, `name`, `created_date`, `recorded` FROM `groups` WHERE `key` = ? && `recorded` = 1 ORDER BY `id` DESC LIMIT ?";
    }

    $st = $db->conn->prepare($query);
    if ($st === false) {
        die("Error preparing the statement: " . $db->conn->error);
    }

    if( $group_key === null ){
        $st->bind_param("i", $display_limit );
    }else{
        $st->bind_param("si", $group_key, $display_limit );
    }

    if (!$st->execute()) {
        die("Execution failed: " . $st->error);
    }
    $st->bind_result($id, $key, $name, $created_date, $recorded);
    $st->store_result();
    $ids = [];

    while ($st->fetch()) {
        $ids[] = $id;
        $font_details[ $id ] = array(
            'id' => $id,
            'key' => $key,
            'name' => $name,
            'created_date' => $created_date,
            'recorded' => $recorded
        );
    }

    $st->close();
    $db->close_connection();
    $font_details = get_multiple_fonts_data( $ids, $font_details );

    return $font_details;
}

function get_multiple_fonts_data( $ids, $font_details )
{
    if( count( $ids ) > 0 ) {

        $id = $group_id = $title = $font_name = $size = $price = $created_time = $recorded = null;
        $ids_str = implode(",", $ids);
        $db = new Database();
        $query = " SELECT `id`, `group_id`, `title`, `font_name`, `size`, `price`, `created_time`, `recorded` FROM `fonts` WHERE `recorded` = 1 && `group_id` IN( $ids_str ) ";
        $st = $db->conn->prepare($query);
        $st->execute();
        $st->bind_result($id, $group_id, $title, $font_name, $size, $price, $created_time, $recorded);
        $st->store_result();

        while ($st->fetch()) {
            $font_details[$group_id]['font_details'][] = array(
                'id' => $id,
                'title' => $title,
                'font_name' => $font_name,
                'size' => $size,
                'price' => $price,
                'created_time' => $created_time,
            );
        }

        $db->close_connection();
        $st->close();
    }

    return array_values( $font_details );
}

function make_font_group( $display_limit )
{
    $result_data = get_groups_data( $display_limit );
    $final_result = array();
    $total_fonts = 0;

    foreach ( $result_data as $results ){
        $font_name = '';
        if( is_array( $results['font_details'] ) ){
            $total_fonts = count( $results['font_details'] );
            foreach ( $results['font_details'] as $result ){
                $font_name .= $result['font_name'].', ';
            }
        }
        $final_result[] = array(
            'id'            => $results['id'],
            'name'          => $results['name'],
            'key'           => $results['key'],
            'font_name'     => rtrim( $font_name, " ,") ,
            'counts'        => $total_fonts,
        );
    }

    return $final_result;
}

/**
 * Update a row in the database table.
 *
 * @param string $key The key value for the WHERE clause.
 * @param int $recorded The new value for the 'recorded' column.
 * @param mysqli $conn The MySQLi connection object.
 * @return bool True if the update was successful, false otherwise.
 */
function updateGroupRecorded( $key, $recorded )
{
    $db =new database();
    $sql = "UPDATE `groups` SET `recorded` = ? WHERE `key` = ?";

    if ($stmt = $db->conn->prepare($sql)) {
        $stmt->bind_param("is", $recorded, $key ); // "is" means integer for recorded and string for key
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Execute failed: " . $stmt->error);
        }

        $db->close_connection();
        $stmt->close();
    } else {
        error_log("Prepare failed: " . $db->conn->error);
    }

    return false;
}

function update_group_name( $key, $name )
{
    $db =new database();
    $sql = "UPDATE `groups` SET `name` = ? WHERE `key` = ?";

    if ($stmt = $db->conn->prepare( $sql )) {
        $stmt->bind_param("ss", $name, $key ); // "is" means integer for recorded and string for key
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Execute failed: " . $stmt->error);
        }

        $db->close_connection();
        $stmt->close();
    } else {
        error_log("Prepare failed: " . $db->conn->error);
    }

    return false;
}

function updateFont( $id, $title, $font_name, $size, $price )
{
    $title = sanitize( $title );
    $font_name = sanitize( $font_name );
    $size = sanitize( $size );
    $price = sanitize( $price );
    $id = sanitize( $id );

    $db = new database();

    if ( $db->conn->connect_error ) {
        return false;
    }

    $stmt = $db->conn->prepare("UPDATE `fonts` SET `title` = ?, `font_name` = ?, `size` = ?, `price` = ? WHERE `id` = ?");

    if ($stmt === false) {
        return false;
    }

    $stmt->bind_param("ssiii", $title, $font_name, $size, $price, $id);

    if ($stmt->execute()) {
        $result = true;
    } else {
        $result = false;
    }

    $stmt->close();
    $db->conn->close();

    return $result;
}

function remove_fonts_from_group( $id )
{
    $id = sanitize( $id );
    $db =new database();

    $stmt = $db->conn->prepare("UPDATE `fonts` SET `recorded`= 0 WHERE `id` = ?" );
    $stmt->bind_param("i", $id );

    if ($stmt->execute()) {
        $result = true;
    } else {
        $result = false;
    }

    $stmt->close();
    $db->conn->close();

    return $result;
}

function sanitize( $data )
{
    $db = new Database();
    return htmlspecialchars(strip_tags($db->conn->real_escape_string($data)));
}

function var_test( $data ){
    echo "<pre>";
    var_dump( $data );
    die();
}
