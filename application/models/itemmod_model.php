<?php
class Itemmod_model extends CI_Model {
    protected $tableName = 'item_mod';
    
    protected $oldData = array();
    protected $implicit = false;

	public function __construct(){
		$this->load->database();
	}

	public function save($itemId, $name) {
        $id = $this->lookupInOldData($itemId, $name, $this->implicit);
        if($id !== false){
            return $id;
        }
        $this->db->insert($this->tableName, array(
            'name' => $name
            , 'item_id' => $itemId
            , 'implicit' => ($this->implicit ? 1 : 0)
        ));
        return $this->db->insert_id();
	}
    
    public function saveAll($itemId, $mods, $implicit = true){
        $this->implicit = $implicit;
        $this->loadOldData($itemId);
        foreach($mods as $name){
            $this->save($itemId, $name);
        }
        $this->deleteUnusedOldData($itemId);
    }
    
    public function findBothByItemId($itemId){
        $data = array();
        $all = $this->findByItemId($itemId);
        $this->implicit = false;
        $data['explicit'] = array_filter($all, array($this, '__filterMod'));
        $this->implicit = true;
        $data['implicit'] = array_filter($all, array($this, '__filterMod'));
        return $data;
    }
    
    public function __filterMod($mod){
        return $mod['implicit'] == $this->implicit;
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
    
    protected function lookupInOldData($itemId, $name){
        foreach($this->oldData[$itemId] as $i => $entry){
            if($entry['name'] == $name && $entry['implicit'] == $this->implicit){
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