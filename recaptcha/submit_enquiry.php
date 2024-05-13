<?php

function verifyRecaptcha($secret, $response)
{
    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(
                array(
                    'secret' => $secret,
                    'response' => $response
                )
            )
        )
    );

    $response = curl_exec($curl);

    if ($response === false) {
        return ['success' => false, 'error' => 'cURL error: ' . curl_error($curl)];
    } else {
        return json_decode($response, true);
    }
}

function processFormData($response)
{
    if ($response['success']) {
        if (isset($_POST['name']) && $_POST['name'] !== "" && isset($_POST['email']) && $_POST['email'] !== "") {
            // Perform additional logic here, e.g., save data to the database
            return ['success' => true, 'score' => $response['score']];
        } else {
            return ['success' => false, 'error' => 'Please enter the required fields'];
        }
    } else {
        return ['success' => false, 'error' => 'reCAPTCHA verification failed'];
    }
}

// Main script logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $secret = '6LdRLNMpAAAAAFDzAK-HDiJoCGWgcMCw1JQrfRQP';
    $recaptcha_response = $_POST['recaptcha_response'];

    $recaptcha_result = verifyRecaptcha($secret, $recaptcha_response);

    if ($recaptcha_result['success']) {
        $form_processing_result = processFormData($recaptcha_result);
        echo json_encode($form_processing_result);
    } else {
        echo json_encode($recaptcha_result);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>