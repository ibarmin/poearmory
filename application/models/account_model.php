<?php
class Account_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
    
    public function getAll(){
        $query = $this->db->get('account');
        return $query->result_array();
    }

	public function get($id) {
		$query = $this->db->get_where('account', array('id' => $id));
		return $query->row_array();
	}
}