<?php
class Characteritems_model extends CI_Model {
    protected $tableName = 'character_inventory';
	public function __construct() {
		$this->load->database();
	}
    
    public function link($data){
        $condition = array(
            'character_id' => $data['character_id']
            , 'inventory_id' => $data['inventory_id']
            , 'active' => 1
            , 'x' => $data['x']
            , 'y' => $data['y']
        );
        $query = $this->db->where($condition)
            ->get($this->tableName);
        if($query->num_rows()){
            $row = $query->row_array();
            if($row['item_id'] === $data['item_id']){
                    return;
            }
            $condition['item_id'] = $row['item_id'];
            if($row['item_id']){
                $this->db->where($condition)
                    ->update($this->tableName, array(
                        'active' => 0
                    ));
            }else{
                $this->db->delete($this->tableName, $condition);                
            }
        }
        $this->db->insert($this->tableName, $data);
    }
    
    public function getCharactersForItem($id){
         $query = $this->db->select(array('character.*', 'equipped'))
            ->join('character', 'character_id = id')
            ->where(array('item_id' => $id))
            ->order_by('equipped', 'DESC')
            ->get($this->tableName);
        if(!$query->num_rows()){
            return array();
        }
        return $query->result_array();
    }
}