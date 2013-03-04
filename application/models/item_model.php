<?php
class Item_model extends CI_Model {
    protected $tableName = 'item';
	public function __construct(){
		$this->load->database();
	}

	public function get($id) {
        $query = $this->db->select('*')
            ->where(array('id' => $id))
            ->get($this->tableName);
        if(!$query->num_rows()){
            return false;
        }
		return $query->row_array();
	}
    
    public function save($data){
        if(($id = $this->findIdByHash($data['hash'])) === false){
            $this->db->insert($this->tableName, $data);
            return $this->findIdByHash($data['hash']);
        }
        $this->db->where('id', $id);
        $this->db->update($this->tableName, $data);
        return $id;
    }

	public function findIdByHash($hash) {
        $query = $this->db->select('id')
            ->where(array('hash' => $hash))
            ->get($this->tableName);
        if(!$query->num_rows()){
            return false;
        }
        $row = $query->row_array();
        return $row['id'];
	}

	public function findByCharacter($id) {
        $query = $this->db->select(array('inventory.name AS inventory_name', 'item_type.name AS type', $this->tableName . '.*'))
            ->join('inventory', 'inventory.id = inventory_id')
            ->join('item_type', 'item_type.id = item_type_id')
            ->join('character_inventory', $this->tableName . '.id = item_id AND active = 1')
            ->where(array('character_id' => $id))
            ->order_by('inventory.id', 'ASC')
            ->order_by('character_inventory.x', 'ASC')
            ->get($this->tableName);
        if(!$query->num_rows()){
            return false;
        }
        return $query->result_array();
	}
}