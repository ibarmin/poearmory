<?php
class Character_model extends CI_Model {
    protected $tableName = 'character';
	public function __construct(){
		$this->load->database();
	}

	public function getCharacters($accountId) {
        $query = $this->db->select(array('character.id', 'character.name', 'level', 'league.name AS league', 'class.name AS class'))
            ->join('class', 'class.id = class_id')
            ->join('league', 'league.id = league_id')
            ->where(array('account_id' => $accountId))
            ->get($this->tableName);
        if(!$query->num_rows()){
            return false;
        }
		return $query->result_array();
	}
    
    public function save($data){
        if(isset($data['id'])){
            $userId = $data['id'];
        }else if(($userId = $this->findId($data['name'])) === false){
            $this->db->insert($this->tableName, $data);
            return $this->findId($data['name']);
        }
        $this->db->where('id', $userId);
        unset($data['name']);
        $this->db->update($this->tableName, $data);
        return $userId;
    }

	public function findBy($col, $value) {
        $query = $this->db->select(array('character.id', 'character.name', 'level', 'league.name AS league', 'class.name AS class', 'url'))
            ->join('class', 'class.id = class_id')
            ->join('league', 'league.id = league_id')
            ->where(array('character.' . $col => $value))
            ->get($this->tableName);
        if(!$query->num_rows()){
            return false;
        }
        return $query->row_array();
	}

	public function findByName($name) {
        return $this->findBy('name', $name);
	}
    
    public function findId($name) {
        $query = $this->db->select('id')
            ->where(array('name' => $name))
            ->get($this->tableName);
        if(!$query->num_rows()){
            return false;
        }
        $row = $query->row_array();
        return $row['id'];
    }
}