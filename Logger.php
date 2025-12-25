<?php
// Logger.php
class Logger {
    private static $logFile = 'log.txt';

    private static function getSessionId() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return substr(session_id(), 0, 8); // Short ID for readability
    }

    public static function log($message) {
        $timestamp = date("Y-m-d H:i:s");
        $sid = self::getSessionId();
        $entry = "[$timestamp] [Session:$sid] $message" . PHP_EOL;
        file_put_contents(self::$logFile, $entry, FILE_APPEND);
    }

    public static function logSection($title) {
        $sid = self::getSessionId();
        $line = str_repeat("-", 60);
        $entry = PHP_EOL . $line . PHP_EOL . 
                 "[$sid] $title - " . date("Y-m-d H:i:s") . PHP_EOL . 
                 $line . PHP_EOL;
        file_put_contents(self::$logFile, $entry, FILE_APPEND);
    }

    public static function logData($label, $data) {
        $formattedData = is_array($data) ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $data;
        self::log("$label: $formattedData");
    }
}
?>
