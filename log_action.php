<?php
// log_action.php
require_once 'Logger.php';

// Allow from any origin for now, or restrict if needed
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? 'Unknown Action';
    $details = $input['details'] ?? '';

    $logMessage = "CLIENT-SIDE: Action: $action | $details";
    Logger::log($logMessage);

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
}
?>
