<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Armory extends CI_Controller {
    protected $inventories = false;
    
	public function __construct() {
		parent::__construct();
		$this->load->model('account_model');
		$this->load->model('character_model');
		$this->load->model('league_model');
		$this->load->model('inventory_model');
		$this->load->model('item_model');
		$this->load->model('itemtype_model');
		$this->load->model('itemmod_model');
		$this->load->model('characteritems_model');
		$this->load->model('requirement_model');
		$this->load->model('property_model');
		$this->load->model('socket_model');
        $this->load->library('poeapi');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('security');
	}

	public function index(){
	    $account = $this->account_model->get(1);
		$data = array(
			'title' => $account['username']
			, 'account' => $account
			, 'characters' => $this->character_model->getCharacters($account['id'])
		);
		$this->load->view('templates/header', $data);
		$this->load->view('armory/index', $data);
		$this->load->view('templates/footer');
	}

	public function import(){
        $account = $this->account_model->get(1);
		$data = array(
			'title' => 'Import characters'
			, 'account' => $account
		);
		$this->load->view('templates/header', $data);
		$this->load->view('armory/import', $data);
		$this->load->view('templates/footer');
	}

	public function view($name){
        $character = $this->buildItems($name);
        if($character === false){
            redirect('/armory/index');
            return;
        }
		$data = array(
			'title' => 'Exile ' . $character['name'] . ' (' . $character['level'] . ')'
            , 'contentTitle' => $character['name']
			, 'character' => $character
		);
		$this->load->view('templates/header', $data);
		$this->load->view('armory/view', $data);
		$this->load->view('templates/footer');
	}

	public function doimport(){
         if($this->input->post('sessid') == ''){
            redirect('/armory/index');
            return;
        }
        $account = $this->account_model->get(1);
        $id = $account['id'];
        $characters = $this->poeapi->getCharacters(array('sessid' => $this->input->post('sessid')));
        if($characters === false){
            redirect('/armory/index');
            return false;
        }
        foreach($characters as $character){
            $characterId = $this->saveCharacter($id, $character);
            $this->saveItemsForCharacter(array(
                'id' => $characterId
                , 'name' => $character['name']
            ));
        }
        redirect('/armory/index');
	}
    
    public function refresh($name){
        $character = $this->character_model->findByName($name);
        if($character === false){
            redirect('/armory/index');
            return;
        }
		$data = array(
			'title' => 'Refresh items on ' . $character['name']
			, 'character' => $character
		);
		$this->load->view('templates/header', $data);
		$this->load->view('armory/refresh', $data);
		$this->load->view('templates/footer');
        
    }
    
    public function dorefresh($name){
        $characterId = $this->character_model->findId($name);
        if($characterId === false){
            redirect('/armory/index');
        }
        $characters = $this->poeapi->getCharacters(array('sessid' => $this->input->post('sessid')));
        if($characters === false){
            redirect('/armory/index');
        }
        foreach($characters as $character){
            if($character['name'] !== $name){
                continue;
            }
            $this->character_model->save(array(
                'id' => $characterId
                , 'league_id' => $this->league_model->getId($character['league'])
                , 'level' => $character['level']
                , 'url' => $this->poeapi->getPassiveSkillsLink($character)
            ));
        }
        $this->saveItemsForCharacter(array('id' => $characterId, 'name' => $name));
        redirect('/exile/' . $name);
    }
    
    protected function saveCharacter($accountId, $data, $verified = 1){
        return $this->character_model->save(array(
            'account_id' => $accountId
            , 'league_id' => $this->league_model->getId($data['league'])
            , 'class_id' => $data['classId']
            , 'name' => $data['name']
            , 'level' => $data['level']
            , 'url' => $this->poeapi->getPassiveSkillsLink($data)
            , 'verified' => $verified
        ));
    }
    
    protected function saveItemsForCharacter($character){
        $this->prepareInventoryList();
        $data = $this->poeapi->getItems(array('sessid' => $this->input->post('sessid'), 'name' => $character['name']));
        foreach ($this->inventories as $inventory) {
            for($x = 0; $x < $inventory['x'];){
                $item = $this->selectItemFromInventory($inventory['name'], $x, $data['items']);
                if($item !== false){
                    $itemId = $this->saveItem($item);
                    $width = $item['w'];
                }else{
                    $itemId = 0;
                    $width = 1;
                }
                $this->characteritems_model->link(array(
                    'character_id' => $character['id']
                    , 'item_id' => $itemId
                    , 'inventory_id' => $inventory['id']
                    , 'active' => 1
                    , 'x' => $x
                    , 'y' => 0
                ));
                $x += $width;
            }
        }        
    }
    
    protected function prepareInventoryList(){
        if(is_array($this->inventories)){
            return;
        }
        $this->inventories = $this->inventory_model->getAll();
    }
    
    protected function saveItem($data){
        $sockets = $data['sockets'];
        $properties = isset($data['properties']) ? $data['properties'] : array();
        $explicitMods = isset($data['explicitMods']) ? $data['explicitMods'] : array();
        $implicitMods = isset($data['implicitMods']) ? $data['implicitMods'] : array();
        $requirements = isset($data['requirements']) ? $data['requirements'] : array();
        $item = $this->itemFromArray($data);
        if($item['inventory_id'] === false){
            return false;
        }
        $item['hash'] = do_hash($item['name'] . $item['type_line'] . $item['item_type_id'] . json_encode($properties) . json_encode($implicitMods) . json_encode($explicitMods) . json_encode($sockets));
        $itemId = $this->item_model->save($item);
        $this->itemmod_model->saveAll($itemId, $implicitMods, true);
        $this->itemmod_model->saveAll($itemId, $explicitMods, false);
        $this->requirement_model->saveAll($itemId, $requirements);
        $this->property_model->saveAll($itemId, $properties);
        foreach($data['socketedItems'] as $socketedItem){
            $sockets[$socketedItem['socket']]['item_id'] = $this->saveItem($socketedItem);
        }
        $this->socket_model->saveAll($itemId, $sockets);
        return $itemId;
    }
    
    protected function itemFromArray($data){
        return array(
            'name' => $data['name']
            , 'type_line' => $data['typeLine']
            , 'flavour' => isset($data['flavourText']) ? implode("\n", $data['flavourText']) : null
            , 'descr_text' => isset($data['descrText']) ? $data['descrText'] : null
            , 'w' => $data['w']
            , 'h' => $data['h']
            , 'icon' => $data['icon']
            , 'colour' => isset($data['colour']) ? $data['colour'] : null
            , 'item_type_id' => $this->itemtype_model->findId($data['frameType'])
            , 'inventory_id' => isset($data['inventoryId']) ? $this->inventory_model->findId($data['inventoryId']) : 0
        );
    }
    
    protected function selectItemFromInventory($inventory, $x, $items){
        foreach($items as $item) {
            if(
                (isset($item['inventory_id']) && $item['inventory_id'] === $inventory || $item['inventoryId'] == $inventory)
                && $item['x'] == $x
           ){
               return $item;
           }
        }
        return false;
    }
    
    protected function buildItems($name){
        $character = $this->character_model->findByName($name);
        if($character === false){
            return false;
        }
        $this->prepareInventoryList();
        $items = $this->item_model->findByCharacter($character['id']);
        $character['inventory'] = array();
        
        foreach($this->inventories as $inventory){
            $data = array();
            foreach($items as $item){
                if($item['inventory_name'] != $inventory['name']){
                    continue;
                }
                $item['property'] = $this->property_model->findByItemId($item['id']);
                $item['requirement'] = $this->prepareRequirements($this->requirement_model->findByItemId($item['id']));
                $mods = $this->itemmod_model->findBothByItemId($item['id']);
                $item['implicit'] = $mods['implicit'];
                $item['explicit'] = $mods['explicit'];
                $data[] = $item;
            }
            $character['inventory'][$inventory['name']] = $data;
        }
        
        return $character;
    }
    
    protected function prepareRequirements($data){
        $ret = array();
        foreach($data as $i){
            $values = json_decode($i['values'], true);
            if($i['display_mode']){
                $ret[] = '<strong>' . $values[0][0] . '</strong>&nbsp;' . $i['name'];
            }else{
                $ret[] = $i['name'] . '&nbsp;<strong>' . $values[0][0] . '</strong>';
            }
        }
        return $ret;
    }
}