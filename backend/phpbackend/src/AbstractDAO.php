<?php namespace hu\doxasoft\phpbackend;

/**
 * Class AbstractDAO
 *
 * @property DB $db
 */
abstract class AbstractDAO {
    protected $db;

    public function __construct() {
        $this->db = new DB();
    }

    /**
     * @return \stdClass[]|object[]
     */
    abstract public function getAll();

    /**
     * @param int $id
     * @return \stdClass|object
     */
    abstract public function get($id);

    /**
     * @param \stdClass|object $dataRB
     * @return \stdClass|object
     */
    abstract public function add($dataRB);

    /**
     * @param \stdClass|object $dataRB
     * @return \stdClass|object
     */
    abstract public function edit($dataRB);

    /**
     * @param int $id
     * @return boolean TRUE if deletion was successful FALSE otherwise
     */
    abstract public function delete($id);
}
