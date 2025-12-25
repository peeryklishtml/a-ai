<?php
// track.php
require_once 'FacebookCAPI.php';
require_once 'Logger.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        Logger::log("Track.php Error: Invalid JSON received");
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
        exit;
    }

    $eventName = $input['event_name'] ?? 'InitiateCheckout';
    Logger::log("Track.php: Received request for '$eventName'");
    
    // Extract User Data
    $userData = [
        'em' => $input['email'] ?? null,
        'ph' => $input['phone'] ?? null,
        'ct' => $input['city'] ?? null,
        'st' => $input['state'] ?? null,
        'zp' => $input['zip'] ?? null,
        // Name splitting if 'name' is full name
        // Simple logic: first part is FN, rest is LN. 
        // This is imperfect but standard for simple integrations.
    ];

    if (!empty($input['name'])) {
        $parts = explode(' ', trim($input['name']), 2);
        $userData['fn'] = $parts[0] ?? '';
        $userData['ln'] = $parts[1] ?? '';
    }

    // Custom Data
    $customData = [
        'currency' => 'BRL',
        'value' => $input['value'] ?? 0,
        'content_name' => $input['content_name'] ?? 'Produto',
    ];

    $capi = new FacebookCAPI();
    $result = $capi->sendEvent($eventName, $userData, $customData);

    echo json_encode($result);
}
?>
