<?php

function get_uploaded_file_names( $fontFolder ){
    $fontFiles = [];

// Check if the folder exists
    if ( is_dir( $fontFolder ) ) {
        // Get all files in the 'fonts' folder
        $files = scandir( $fontFolder );

        // Loop through each file in the folder
        foreach ($files as $file) {
            // Skip the current and parent directory entries
            if ($file !== '.' && $file !== '..' && pathinfo( $file, PATHINFO_EXTENSION) === 'ttf' ) {
                $fontFiles[] = $file;
            }
        }
    }

    return $fontFiles;
}

function generateMd5Key( $groupName ) {
    // Generate the MD5 hash of the group name
    $md5Hash = md5($groupName);

    // Truncate the hash to 12 characters
    $shortKey = substr($md5Hash, 0, 12);

    return $shortKey;
}
function insert_group_data( $key, $name ) {

    $db = new Database();
    $stmt = $db->conn->prepare("INSERT INTO groups (`key`, `name`) VALUES ( ?, ? )");
    // Bind parameters to the prepared statement
    $stmt->bind_param("ss", $key, $name);
    // Execute the statement
    if ($stmt->execute()) {
        // Get the last inserted ID
        $last_id = $db->conn->insert_id;
        $stmt->close();
        return $last_id; // Return the last inserted ID
    } else {
        $stmt->close();
        return false; // Return false if the insert failed
    }
}

function insert_font_data($group_id, $data) {
    // Create an instance of the Database class
    $db = new Database();

    // Check if the connection was successful
    if ($db->conn->connect_error) {
        die("Connection failed: " . $db->conn->connect_error);
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $db->conn->prepare("INSERT INTO ` fonts` (`group_id`, `title`, `font_name`, `size`, `price`) VALUES (?, ?, ?, ?, ?)");

    // Check if the prepare was successful
    if ($stmt === false) {
        die("Error preparing the statement: " . $db->conn->error);
    }

    // Bind parameters for each insert
    $result = 1; // Initialize result to success
    for ($i = 0; $i < count($data['title']); $i++) {
        $title = sanitize($data['title'][$i]);
        $fontName = sanitize($data['font_name'][$i]);
        $size = sanitize($data['size'][$i]);
        $price = sanitize($data['price'][$i]);

        // Re-bind the parameters in each iteration
        $stmt->bind_param("issii", $group_id, $title, $fontName, $size, $price);

        // Execute the prepared statement
        if (!$stmt->execute()) {
            // If any insertion fails, mark as failure
            $result = 0;
            break;
        }
    }

    // Close the statement
    $stmt->close();

    // Close the database connection
    $db->closeConnection();

    return $result;
}


function get_groups_data1( $display_limit ){
    $db = new Database();
    $groups=$font_details=array();
    $id = $key = $name = null;
    $query="SELECT `id`, `key`, `name`, `created_date`, `recorded` FROM `groups` WHERE `recorded` = 1 ORDER BY `id` DESC LIMIT $display_limit";
    echo $query;
    $st = $db->conn->prepare($query);
    $st->execute();
    $st->bind_result( $id ,$key, $name );
    $st->store_result();
    while($st->fetch()){
        $ids[]=$id;
        $font_details[ $id ]= array(
            'key'=>$key,
            'name'=>$name
        );
    }
    $st->close();
    $font_details=get_multiple_fonts_data( $ids );

//    var_dump( $font_details );

    return $font_details;
}

function get_groups_data($display_limit) {
    $db = new Database();
    $font_details = array();
    $id = $key = $name = $created_date = $recorded = null;
    $query = "SELECT `id`, `key`, `name`, `created_date`, `recorded` FROM `groups` WHERE `recorded` = 1 ORDER BY `id` DESC LIMIT ?";
    $st = $db->conn->prepare($query);
    if ($st === false) {
        die("Error preparing the statement: " . $db->conn->error);
    }

    $st->bind_param("i", $display_limit); // "i" for integer

    if (!$st->execute()) {
        die("Execution failed: " . $st->error);
    }
    $st->bind_result($id, $key, $name, $created_date, $recorded);
    $st->store_result();
    $ids = []; // Initialize an array to store IDs
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
    $font_details = get_multiple_fonts_data( $ids, $font_details );

    return $font_details;
}

function get_multiple_fonts_data( $ids, $font_details ){
    $id = $group_id = $title = $font_name = $size = $price = $created_time = $recorded = null;
    $ids_str=implode(",", $ids );
    $db = new Database();
    $query = " SELECT `id`, `group_id`, `title`, `font_name`, `size`, `price`, `created_time`, `recorded` FROM ` fonts` WHERE `recorded`=1 AND `group_id` IN( $ids_str ) ";
    $st = $db->conn->prepare( $query );
    $st->execute();
    $st->bind_result( $id,$group_id, $title, $font_name, $size, $price, $created_time, $recorded );
    $st->store_result();
    while($st->fetch()){

        $font_details[$group_id]['font_details'][]= array(
            'id'=>$id,
            'title'=>$title,
            'font_name'=>$font_name,
            'size'=>$size,
            'price'=>$price,
            'created_time'=>$created_time,
        );

    }
    $st->close();

    return array_values( $font_details );
}

function make_font_group(){
    $display_limit = 100;
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
            'id' => $results['id'],
            'name' => $results['name'],
            'key' => $results['key'],
            'font_name' => $font_name,
            'counts' => $total_fonts,
        );
    }

    return $final_result;
}

function sanitize( $data ) {
    return  $data;
//    return htmlspecialchars(strip_tags($this->conn->real_escape_string($data)));
}