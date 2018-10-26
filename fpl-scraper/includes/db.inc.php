<?php
/**
 * Class DB
 */
class DB {
    /** @var array */
    protected $options = array();
    /** @var array */
    static protected $instances = array();
    /** @var string */
    protected $error = null;

    /**
     * @throws Exception
     */
    public function __clone() {
        throw new Exception('Cloning is not allowed.');
    }

    /**
     * @param array $options
     * @return mixed
     */
    public static function get_instance($options = array()) {
        $key = sha1(serialize($options));

        if (empty(self::$instances[$key])) {
            self::$instances[$key] = new DB($options);
        }

        return self::$instances[$key];
    }

    /**
     * @param array $options
     */
    protected function __construct($options) {
        $this->options = $options;

        $dsn = sprintf(
            "mysql:dbname=%s;host=%s",
            $this->options['database'],
            $this->options['host']
        );

        try {
            $this->db = new PDO(
                $dsn,
                $this->options['user'],
                $this->options['pass'],
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                )
            );
        } catch (PDOException $e) {
            error_log('[FFS Importer]: Error connecting to database');
        }
    }


    public function __destruct() {
        unset($this->db);
    }

    /**
     * @param string $stmt
     * @param array $params
     * @param int $start
     * @param int $count
     * @return array|bool
     */
    public function get_array($stmt, $params = array(), $start = 0, $count = 0) {
        try {
            if ($count > 0) {
                $stmt = preg_replace('/^select\s+/i', 'select sql_calc_found_rows ', sprintf('%s limit %d, %d', trim($stmt), $start, $count));
            }

            if (empty($params) && $result = $this->db->query($stmt)) {
                return $result->fetchAll(PDO::FETCH_ASSOC);
            } elseif (($stmt = $this->db->prepare($stmt)) && $stmt->execute($params)) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log(sprintf(
                'Error getting array (%s - %d)',
                $e->getMessage(),
                $e->getCode())
            );
        }

        return false;
    }

    /**
     * @param string $stmt
     * @param array $params
     * @return array|bool
     */
    public function get_list($stmt, $params = array()) {
        if (is_array($results = $this->get_array($stmt, $params))) {
            $list = array();
            foreach ($results as $result) {
                $list[] = current($result);
            }
            return $list;
        }
        return false;
    }

    /**
     * @param string $stmt
     * @param array $params
     * @return bool|mixed
     */
    public function get_row($stmt, $params = array()) {
        try {
            if (empty($params) && $result = $this->db->query($stmt)) {
                return $result->fetch(PDO::FETCH_ASSOC);
            } elseif (($stmt = $this->db->prepare($stmt)) && $stmt->execute($params)) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log(sprintf(
                'Error getting row (%s - %d)',
                $e->getMessage(),
                $e->getCode())
            );
        }

        return false;
    }

    /**
     * @param string $stmt
     * @param array $params
     * @param int $column
     * @return bool|string
     */
    public function get_column($stmt, $params = array(), $column = 0) {
        try {
            if (empty($params) && $result = $this->db->query($stmt)) {
                return $result->fetchColumn($column);
            } elseif (($stmt = $this->db->prepare($stmt)) && $stmt->execute($params)) {
                return $stmt->fetchColumn($column);
            }
        } catch (PDOException $e) {
            error_log(sprintf(
                'Error getting column (%s - %d)',
                $e->getMessage(),
                $e->getCode())
            );
        }

        return false;
    }

    /**
     * @param string $stmt
     * @param array $params
     * @param int $start
     * @param int $count
     * @return bool|PDOStatement|string
     */
    public function get_recordset($stmt, $params = array(), $start = 0, $count = 0) {
        try {
            if ($count > 0) {
                $stmt = sprintf('%s limit %d, %d', $stmt, $start, $count);
            }

            if (empty($params) && $result = $this->db->query($stmt)) {
                return $result;
            } elseif (($stmt = $this->db->prepare($stmt)) && $stmt->execute($params)) {
                return $stmt;
            }
        } catch (PDOException $e) {
            error_log(sprintf(
                'Error getting recordset (%s - %d)',
                $e->getMessage(),
                $e->getCode())
            );
        }

        return false;
    }

    /**
     * @param string $stmt
     * @param array $params
     * @return bool|PDOStatement
     */
    public function run($stmt, $params = array()) {
        try {
            if (empty($params)) {
                return $this->db->query($stmt);
            } elseif ($stmt = $this->db->prepare($stmt)) {
                return $stmt->execute($params);
            }
        } catch (PDOException $e) {
            error_log(sprintf(
                'Error running query (%s - %d)',
                $e->getMessage(),
                $e->getCode())
            );
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function get_total_rows() {
        try {
            if ($result = $this->db->query('select found_rows()')) {
                return $result->fetchColumn(0);
            }
        } catch (PDOException $e) {
            error_log(sprintf(
                'Error getting total number of rows (%s - %d)',
                $e->getMessage(),
                $e->getCode())
            );
        }

        return false;
    }

    /**
     * @param int $page
     * @param int $numPerPage
     * @return array
     */
    public function get_offsets_from_page($page, $numPerPage = 50) {
        $start = ($page - 1) * $numPerPage;

        return array($start, $numPerPage);
    }

    /**
     * @param int $page
     * @param int $numPerPage
     * @return array
     */
    public function get_paging($page, $numPerPage = 50) {
        $numRows = $this->get_total_rows();
        $numPages = ceil($numRows / $numPerPage);
        $pages = array();

        $pages['total'] = $numRows;

        if ($page > 1) {
            $pages['previous'] = true;
        }

        if (($page - 4) > 1) {
            $pages['previous-n'] = true;
        }

        if ($page <= 5) {
            $pages['start'] = 1;
        } elseif ($numPages - $page < 4) {
            $pages['start'] = $page - (8 - ($numPages - $page));
        } else {
            $pages['start'] = $page - 4;
        }

        if ($page >= $numPages - 4) {
            $pages['end'] = $numPages;
        } elseif ($page <= 4) {
            $pages['end'] = $page + (9 - $page);
        } else {
            $pages['end'] = $page + 4;
        }

        if (($page + 4) < $numPages) {
            $pages['next-n'] = true;
        }

        if ($page < $numPages) {
            $pages['next'] = true;
        }

        $pages['current'] = $page;
        $pages['num-pages'] = $numPages;

        $pages['first-result'] = ($pages['current'] - 1) * $numPerPage + 1;
        $pages['last-result'] = ($pages['current'] - 1) * $numPerPage + $numPerPage;

        if ($pages['last-result'] > $pages['total']) {
            $pages['last-result'] = $pages['total'];
        }

        return $pages;
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function __call($name, $params) {
        return call_user_func_array(array($this->db, $name), $params);
    }
    
    /**
     * @param array $array
     * @return string 
     */
    public function escape_in_values($array) {
        foreach ($array as &$item) {
            $item = $this->quote($item);
        }
        return implode(',', $array);
    }
}
