<?php
/**
 * Class Model
 */
abstract class Model {
    /** @var DB */
    protected $db;

    public function __construct() {
        $this->db = DB::get_instance(array(
            'host' => FFS_DB_HOST,
            'database' => FFS_DB_DATABASE,
            'user' => FFS_DB_USER,
            'pass' => FFS_DB_PASS
        ));
    }

    public function __destruct() {
        unset($this->db);
    }
}
