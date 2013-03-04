<?php
class Itemtype_model extends CI_Model {
    protected $tableName = 'item_type';

	public function __construct(){
		$this->load->database();
	}

	public function findId($frametype) {
		$query = $this->db->get_where($this->tableName, array('frametype' => $frametype));
        $row = $query->row_array();
		return isset($row['id']) ? $row['id'] : false;
	}
}