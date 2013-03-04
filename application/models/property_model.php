<?php
class Property_model extends CI_Model {
    protected $tableName = 'property';
    
    protected $oldData = array();

	public function __construct(){
		$this->load->database();
	}

	public function save($itemId, $data) {
        $id = $this->lookupInOldData($itemId, $data['name']);
        if($id !== false){
            return $id;
        }
        $this->db->insert($this->tableName, array(
            'name' => $data['name']
            , 'item_id' => $itemId
            , 'values' => json_encode($data['values'])
        ));
        return $this->db->insert_id();
	}
    
    public function saveAll($itemId, $data){
        $this->loadOldData($itemId);
        foreach($data as $entry){
            $this->save($itemId, $entry);
        }
        $this->deleteUnusedOldData($itemId);
    }
    
    protected function loadOldData($itemId){
        if(isset($this->oldData[$itemId])){
            return;
        }
        $this->oldData[$itemId] = $this->findByItemId($itemId);        
    }
    
    public function findByItemId($itemId){
        $query = $this->db->where(array('item_id' => $itemId))
            ->get($this->tableName);
        if(!$query->num_rows()){
            return array();
        }
        return $query->result_array();
        
    }
    
    protected function lookupInOldData($itemId, $name){
        foreach($this->oldData[$itemId] as $i => $entry){
            if($entry['name'] == $name){
                $this->oldData[$itemId][$i]['preserve'] = true;
                return $entry['id'];
            }
        }
        return false;
    }
    
    protected function deleteUnusedOldData($itemId){
        foreach ($this->oldData[$itemId] as $entry){
            if(!isset($entry['preserve']) || !$entry['preserve']){
                $this->db->delete($this->tableName, array('id' => $entry['id'])); 
            }
        }
    }
}