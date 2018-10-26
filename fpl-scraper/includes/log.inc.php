<?php
/**
 * Class Log
 */
class Log {
    const LOG_NONE = 0;
    const LOG_SCREEN = 1;
    const LOG_ERROR_LOG = 2;

    /** @var int */
    static protected $logType = self::LOG_SCREEN;

    /**
     * @param string $logType
     */
    static public function set_log_type($logType) {
        self::$logType = $logType;
    }

    /**
     * @return int
     */
    static public function get_log_type() {
        return self::$logType;
    }

    /**
     * @param string $message
     */
    static public function log_message($message) {
        switch (self::$logType) {
            case self::LOG_SCREEN:
                echo "[FFS Importer]: $message\n";
                break;
            case self::LOG_ERROR_LOG:
                error_log("[FFS Importer]: $message\n", 3, FFS_LOG_FILE);
                break;
        }
    }
}
