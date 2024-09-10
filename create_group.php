<?php
// your-server-endpoint.php

// Include your database connection file here
//include 'db_connection.php'; // Update this to your actual DB connection file

$response = array(); // Initialize response array

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    var_dump( $_POST );
    // Get the form data
    $texts = $_POST['text']; // Array of text inputs
    $fonts = $_POST['font']; // Array of font selections
    $number1 = $_POST['number1']; // Array of number1 inputs
    $number2 = $_POST['number2']; // Array of number2 inputs
    $colors = $_POST['color']; // Array of color inputs

    // Validate input
    if (count($texts) < 2 || count($fonts) < 2) {
        $response['status'] = 'error';
        $response['message'] = 'At least two fonts must be selected to create a group.';
        echo json_encode($response);
        exit;
    }

    // Prepare and execute database insertion
    try {
        /*$stmt = $pdo->prepare("INSERT INTO font_groups (text, font, number1, number2, color) VALUES (?, ?, ?, ?, ?)");
        for ($i = 0; $i < count($texts); $i++) {
            $stmt->execute([
                $texts[$i],
                $fonts[$i],
                $number1[$i],
                $number2[$i],
                $colors[$i]
            ]);
        }*/

        // If the insertion is successful, send a success response
        $response['status'] = 'success';
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
echo json_encode($response);
?>
