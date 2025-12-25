<?php

class FacebookCAPI {
    private $pixelId;
    private $accessToken;
    private $version = 'v19.0'; // Or latest version
    
    public function __construct() {
        // Hardcoded credentials as requested by user
        $this->pixelId = '1132621771832154';
        $this->accessToken = 'EAAI6i9o9NFoBQU4dg3AcbzwsisxWmV5b4QvZBT63XYtsZCKJbWh6doN2AbrXkMXSZBvFt9tXy8f8O2T9ZBCjarKo6iGVZBSSl60PJyH27yK3h8PL9jGt9T2RsaD7U3bDyX4HeKuAx6wZAT39rGYL0iiQtE5FH58OytuiSoT6Cr9HPbpV0BxDZArA7CdRgE7OW8WBQZDZD';
    }

    public function sendEvent($eventName, $userData, $customData = [], $eventId = null) {
        $url = "https://graph.facebook.com/{$this->version}/{$this->pixelId}/events?access_token={$this->accessToken}";
        
        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'action_source' => 'website',
                    'user_data' => $this->hashUserData($userData),
                    'custom_data' => array_merge($customData, [
                        'value' => isset($customData['value']) ? round((float)$customData['value'], 2) : 0
                    ]),
                ]
            ]
        ];

        if ($eventId) {
            $payload['data'][0]['event_id'] = $eventId;
        }

        // Add basics if not present (IP, User Agent)
        if (!isset($payload['data'][0]['user_data']['client_ip_address'])) {
            $payload['data'][0]['user_data']['client_ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        if (!isset($payload['data'][0]['user_data']['client_user_agent'])) {
            $payload['data'][0]['user_data']['client_user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        }

        // Log the attempt with details
        require_once 'Logger.php';
        Logger::log("[FACEBOOK] Disparo API: $eventName");
        Logger::log("[FACEBOOK] Pixel ID: {$this->pixelId}");
        
        // Log key user data (masked)
        $logUserData = $userData;
        if(isset($logUserData['em'])) $logUserData['em'] = substr($logUserData['em'], 0, 3) . '***@***';
        if(isset($logUserData['ph'])) $logUserData['ph'] = substr($logUserData['ph'], 0, 4) . '***';
        
        Logger::logData("[FACEBOOK] Payload User Data", $logUserData);
        
        // Log Technical Context (IP/Agent)
        if(isset($payload['data'][0]['user_data']['client_ip_address'])) {
            Logger::log("[FACEBOOK] IP: " . $payload['data'][0]['user_data']['client_ip_address']);
        }
        if(isset($payload['data'][0]['user_data']['client_user_agent'])) {
            Logger::log("[FACEBOOK] User Agent: " . $payload['data'][0]['user_data']['client_user_agent']);
        }

        Logger::logData("[FACEBOOK] Payload Custom Data", $customData);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        // Log result
        if ($err) {
            Logger::log("[FACEBOOK] ERRO CURL: $err");
        } else {
            Logger::log("[FACEBOOK] Resposta API [$httpCode]: " . substr($response, 0, 150)); 
            $respJson = json_decode($response, true);
            if(isset($respJson['events_received'])) {
                Logger::log("[FACEBOOK] Eventos Recebidos: " . $respJson['events_received']);
            }
        }

        return [
            'success' => ($httpCode >= 200 && $httpCode < 300),
            'http_code' => $httpCode,
            'response' => json_decode($response, true),
            'error' => $err
        ];
    }

    private function hashUserData($data) {
        $hashed = [];
        
        // Normalizing and hashing according to FB specs
        if (!empty($data['em'])) {
            $hashed['em'] = hash('sha256', strtolower(trim($data['em'])));
        }
        if (!empty($data['ph'])) {
            // Remove symbols, keep only numbers
            $ph = preg_replace('/\D/', '', $data['ph']);
            $hashed['ph'] = hash('sha256', $ph);
        }
        if (!empty($data['fn'])) {
            $hashed['fn'] = hash('sha256', strtolower(trim($data['fn']))); // First name
        }
        if (!empty($data['ln'])) {
            $hashed['ln'] = hash('sha256', strtolower(trim($data['ln']))); // Last name
        }
        if (!empty($data['ct'])) {
            $hashed['ct'] = hash('sha256', strtolower(trim($data['ct']))); // City
        }
        if (!empty($data['st'])) {
            $hashed['st'] = hash('sha256', strtolower(trim($data['st']))); // State e.g. 'ca'
        }
        if (!empty($data['zp'])) {
            $hashed['zp'] = hash('sha256', preg_replace('/\D/', '', $data['zp'])); // Zip
        }
        if (!empty($data['country'])) {
            $hashed['country'] = hash('sha256', strtolower(trim($data['country'])));
        } else {
             // Default brazil if not provided? Or just leave empty. 
             // Let's assume 'br' if we are confident, but better to be explicit.
             $hashed['country'] = hash('sha256', 'br');
        }

        // Direct pass-through for non-PII or already hashed if needed, 
        // but typically we overwrite with hashed versions above.
        // Also capture fbc/fbp from cookies if available/passed
        if (!empty($data['fbc'])) $hashed['fbc'] = $data['fbc'];
        else if (isset($_COOKIE['_fbc'])) $hashed['fbc'] = $_COOKIE['_fbc'];

        if (!empty($data['fbp'])) $hashed['fbp'] = $data['fbp'];
        else if (isset($_COOKIE['_fbp'])) $hashed['fbp'] = $_COOKIE['_fbp'];
        
        // IP and Client User Agent should be raw
        if (!empty($data['client_ip_address'])) $hashed['client_ip_address'] = $data['client_ip_address'];
        if (!empty($data['client_user_agent'])) $hashed['client_user_agent'] = $data['client_user_agent'];

        return $hashed;
    }
}
?>
