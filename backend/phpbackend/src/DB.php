<?php namespace hu\doxasoft\phpbackend;

use Exception;
use mysqli;
use mysqli_stmt;

/**
 * Class DB
 *
 * @property mysqli $dbconn
 * @property mysqli_stmt $stmt
 */
class DB {
    private $dbconn;
    private $stmt;

    public function __construct() {}

    private function connect() {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ( $conn->connect_error ) {
            die('Nincs kapcsolat az adatbazissal!');
        }
        $utf8_char = $conn->query( "SET CHARACTER SET 'utf8'" );
        $utf8_set = $conn->query( "SET NAMES 'utf8'" );
        if ( ( $utf8_char === FALSE ) || ( $utf8_set === FALSE ) ) {
            throw new Exception('Nem lehet kiválasztani a megfelelő karakterkészletet!');
        }
        $conn->select_db(DB_NAME);
        $this->dbconn = $conn;
    }

    public function prepare( $sql ) {
        if ($this->stmt !== null) {
            $this->done();
        }
        $this->connect();
        $this->stmt = $this->dbconn->prepare($sql);
        return $this->stmt;
    }

    public function done() {
        $this->stmt->close();
        $this->stmt = null;
    }

    /**
     * runs the prepared SQL statement (use it for UPDATE, DELETE, INSERT)
     *
     * @return boolean
     *      NULL if the connection failed
     *      FALSE if there was an error
     *      TRUE on success
     */
    public function update() {
        if ($this->stmt === null) {
            return null;
        }
        $return = $this->stmt->execute();
        $this->done();
        return $return;
    }

    /**
     * runs the prepared SQL statement (use it for UPDATE, DELETE, INSERT)
     *
     * @return int
     *      NULL if the connection failed or there was an error
     *      The ID of the inserted/updated/deleted item
     */
    public function updateReturnID() {
        if ($this->stmt === null) {
            return null;
        }
        $return = $this->stmt->execute();
        if ( $return ) {
            $return = $this->stmt->insert_id;
        }
        $this->done();
        return $return;
    }

    /**
     * runs the prepared SQL statement (use it for SELECT)
     *
     * @return array
     *      NULL if the connection failed or there was no result
     *      an array that contains the result rows of the executed statement
     */
    public function query() {
        $result = $this->stmt->execute();

        // if there was no result
        if ( !$result ) { return null; }

        // if there were results we should place it into an array
        $result = $this->stmt->get_result();
        $return = array();
        while ($row = $result->fetch_object()) {
            $return[] = $row;
        }

        $this->done();
        return $return;
    }

    /**
     * runs the prepared SQL statement (use it for SELECT)
     *
     * @return mixed
     *      NULL if the connection failed or there was no result
     *      The first item of the responded array
     */
    public function queryGetOne() {
        $result = $this->stmt->execute();

        // if there was no result
        if ( !$result ) { return null; }

        // if there were results we should place it into an array
        $result = $this->stmt->get_result();
        $this->done();
        if ($row = $result->fetch_object()) {
            return $row;
        } else {
            return null;
        }
    }

    public function getColumnNames($table) {
        $query = $this->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME=?");
        $query->bind_param('s', $table);
        return $this->query();
    }

}
