<?php
// check_status.php

header('Content-Type: application/json');

// Configuration - Replace with actual credentials
$clientId = "dice_live_0b5adcb282e250521856f84f060c749d";
$clientSecret = "dicesk_live_27f3d80af88c9ee279d5733edfcbc9828391fb4eccbbaf89";
$baseUrl = "https://api.use-dice.com";

$transactionId = $_GET['transaction_id'] ?? '';

if (!$transactionId) {
    echo json_encode(['status' => 'ERROR', 'message' => 'Transaction ID missing']);
    exit;
}

// 1. Authenticate (Ideally cache this token)
$ch = curl_init($baseUrl . '/api/v1/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'client_id' => $clientId,
    'client_secret' => $clientSecret
]));

$authResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 && $httpCode !== 201) {
    echo json_encode(['status' => 'ERROR', 'message' => 'Auth failed']);
    exit;
}

$authData = json_decode($authResponse, true);
$token = $authData['access_token'] ?? ''; // Adjust based on actual response structure if needed

if (!$token) {
    // If token key is different, adjust here. Assuming standard OAuth/JWT response could be 'access_token' or similar.
    // Documentation says "Use suas credenciais para obter um token ... Envie este token no cabeçalho". 
    // Assuming response is JSON with a token field. Let's assume 'token' or 'access_token'.
    // Debug:
    // file_put_contents('auth_log.txt', $authResponse); 
    // Usually it's 'access_token' or just returning the string? API docs often return JSON.
    // We will assume it's in $authData key 'token' or 'access_token'. Let's try to detect.
    $token = $authData['token'] ?? $authData['access_token'] ?? null;
}

if (!$token) {
    echo json_encode(['status' => 'ERROR', 'message' => 'Token not found']);
    exit;
}

// 2. Check Status
$ch = curl_init($baseUrl . '/api/v1/transactions/getStatusTransac/' . $transactionId);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    // Decode to check status internally
    $statusData = json_decode($response, true);
    $status = $statusData['status'] ?? 'UNKNOWN';
    
    // If PAID or COMPLETED, trigger CAPI Purchase if pending
    if ($status === 'PAID' || $status === 'COMPLETED') {
        $orderFile = "orders/{$transactionId}.json";
        
        if (file_exists($orderFile)) {
            require_once 'FacebookCAPI.php';
            require_once 'Logger.php';
            
            $orderData = json_decode(file_get_contents($orderFile), true);
            
            // Send Event
            $capi = new FacebookCAPI();
            $result = $capi->sendEvent('Purchase', $orderData['user_data'], [
                'currency' => 'BRL',
                'value' => $orderData['amount'],
                'transaction_id' => $transactionId,
                'content_name' => $orderData['product_name']
            ], $transactionId);
            
            Logger::log("Check_status.php: Purchase Event Sent for $transactionId. Result: " . json_encode($result));
            
            // Mark as processed (Rename or Delete)
            // Renaming to .paid to keep record but preventing double send
            rename($orderFile, "orders/{$transactionId}.paid");
        }
    }

    echo $response; // Return raw JSON from API to frontend
} else {
    echo json_encode(['status' => 'ERROR', 'message' => 'Status check failed']);
}
?>
