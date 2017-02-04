<?php
namespace hu\chrome\gameoflife\tables;

use hu\doxasoft\phpbackend\AbstractDAO;
use hu\doxasoft\phpbackend\exceptions\DataCreateException;
use hu\doxasoft\phpbackend\exceptions\DataEditException;
use hu\doxasoft\phpbackend\exceptions\DataNotFoundException;

class TablesDAO extends AbstractDAO {

    const GET_TABLES = "SELECT p.* FROM tables p";
    const GET_TABLE = "SELECT p.* FROM tables p WHERE id=?";
    const ADD_TABLE = "INSERT INTO tables(table_name, table_data) VALUES (?, ?)";
    //const EDIT_TABLE = "UPDATE tables SET table_name=?, table_data=? WHERE id=?";

    /**
     * TablesDAO constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns with the list of all stored tables
     * @return mixed[]
     */
    public function getAll() {
        $this->db->prepare(self::GET_TABLES);
        $tables = $this->db->query();
        foreach ($tables as $table) {
            $table->table_data = json_decode($table->table_data);
        }
        return $tables;
    }

    /**
     * Returns with the table of the given id
     * @return mixed
     * @throws DataNotFoundException
     */
    public function get($id) {
        $query = $this->db->prepare(self::GET_TABLE);
        $query->bind_param('i', $id);
        try {
            return $this->db->queryGetOne();
        } catch (\Exception $e) {
            throw new DataNotFoundException("Nem található tábla ezzel az azonosítóval!");
        }
    }

    /**
     * @param mixed $data
     * @return mixed
     * @throws DataCreateException
     */
    public function add($data) {
        $json_data = json_encode($data->table);
        $query = $this->db->prepare(self::ADD_TABLE);
        $query->bind_param('ss', $data->tableName, $json_data);
        $id = $this->db->updateReturnID();
        if (!$id) {
            throw new DataCreateException("Nem sikerült létrehozni a táblát!");
        }
        $data->id = $id;
        return $data;
    }

    public function edit($data) {

    }

    public function delete($id) {

    }
}
