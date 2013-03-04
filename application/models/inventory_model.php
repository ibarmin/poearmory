<?php
class Inventory_model extends CI_Model {
    protected $tableName = 'inventory';

	public function __construct(){
		$this->load->database();
	}

	public function findId($name) {
		$query = $this->db->select('id')->get_where($this->tableName, array('name' => $name));
        $row = $query->row_array();
		return isset($row['id']) ? $row['id'] : false;
	}
    
    public function getId($name){
        $leagueId = $this->findId($name);
        return $leagueId ?: $this->league_model->add($name);
    }
    
    public function add($name){
        $r = $this->db->insert($this->tableName, array('name' => $name));
        return $r ? $this->db->insert_id() : $r;
    }
    
    public function getAll(){
        return $this->db->get($this->tableName)->result_array();
    }
    
    public function get($id){
 		$query = $this->db->get_where($this->tableName, array('id' => $id));
        return $query->row_array();
   }
}