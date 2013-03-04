<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Item extends CI_Controller {
    protected $inventories = false;
    
	public function __construct() {
		parent::__construct();
		$this->load->model('item_model');
		$this->load->model('itemtype_model');
		$this->load->model('itemmod_model');
		$this->load->model('characteritems_model');
		$this->load->model('requirement_model');
		$this->load->model('property_model');
		$this->load->model('socket_model');
        $this->load->helper('url');
	}

	public function view($id){
	    $item = $this->item_model->get($id);
        if($item === false){
            redirect('/armory/index');
        }
		$data = array(
			'title' => $item['name'] ?: $item['type_line']
            , 'contentTitle' => $item['name'] ?: '&nbsp;'
			, 'item' => $item
			, 'characters' => $this->characteritems_model->getCharactersForItem($id)
		);
		$this->load->view('templates/header', $data);
		$this->load->view('item/view', $data);
		$this->load->view('templates/footer');
	}
}