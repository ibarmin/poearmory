<?php
class Socket_model extends CI_Model {
    protected $tableName = 'socket';
    
    protected $oldData = array();

	public function __construct(){
		$this->load->database();
	}

	public function save($itemId, $data) {
        $id = $this->lookupInOldData($itemId, $data);
        if($id !== false){
            return $id;
        }
        $this->db->insert($this->tableName, array(
            'attr' => $data['attr']
            , 'parent_id' => $itemId
            , 'group' => json_encode($data['group'])
            , 'item_id' => isset($data['item_id']) ? $data['item_id'] : 0
        ));
        return $this->db->insert_id();
	}
    
    public function saveAll($itemId, $data){
        $this->loadOldData($itemId);
        foreach($data as $i => $entry){
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
    
    protected function findByItemId($itemId){
        $query = $this->db->where(array('item_id' => $itemId))
            ->get($this->tableName);
        if(!$query->num_rows()){
            return array();
        }
        return $query->result_array();
        
    }
    
    protected function lookupInOldData($itemId, $data){
        foreach($this->oldData[$itemId] as $i => $entry){
            if(
                $entry['attr'] == $data['attr']
                && $entry['group'] == $data['group']
                && $entry['item_id'] == $data['item_id']
            ){
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