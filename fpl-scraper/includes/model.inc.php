<?php
/**
 * Class Model
 */
abstract class Model {
    /** @var DB */
    protected $db;

    public function __construct() {
        $this->db = DB::get_instance(array(
            'host' => X4FPL_DB_HOST,
            'database' => X4FPL_DB_DATABASE,
            'user' => X4FPL_DB_USER,
            'pass' => X4FPL_DB_PASS
        ));
    }

    public function __destruct() {
        unset($this->db);
    }
}
