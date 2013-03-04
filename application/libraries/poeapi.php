<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Poeapi {
    protected $sessId = false;
    protected $cookies = array();
    protected $urls = array(
        'get-characters' => 'http://www.pathofexile.com/character-window/get-characters'
        , 'get-passive-skills' => 'http://www.pathofexile.com/character-window/get-passive-skills'
        , 'get-items' => 'http://www.pathofexile.com/character-window/get-items'
    );
    protected $userAgent = 'Mozilla/5.0 (Windows NT 6.0) PoE Armory v0.1 r5';
    protected $errors = array();
    
    public function getCharacters($params){
        $this->cookies = array('PHPSESSID=' . $params['sessid']);
        return $this->requestInfo($this->urls['get-characters'], array());
    }
    
    public function getItems($params){
        $this->cookies = array('PHPSESSID=' . $params['sessid']);
        return $this->requestInfo($this->urls['get-items'], array('character' => $params['name']));
    }
    
    public function getPassiveSkillsLink($character){
        $data = $this->requestInfo($this->urls['get-passive-skills'], array('character' => $character['name']));
        if(!$data){
            return false;
        }
        return $this->buildPassiveSkillsLink($character['classId'], $data['hashes']);
    }
    
    public function getErrors(){
        return $this->errors;
    }

	protected function requestInfo($url, $post){
        $curlOptions = array(
            CURLOPT_URL => $url,
            CURLOPT_VERBOSE => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => is_array($post) && count($post) > 0,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_COOKIE => implode(';', $this->cookies)
        );
        $request = curl_init();
        curl_setopt_array($request, $curlOptions);
        $json = curl_exec($request);
        if(!$json){
            $this->errors[] = curl_error($request);
            curl_close($request);
            return false;
        }
        curl_close($request);
        return json_decode($json, true);
	}

    protected function buildPassiveSkillsLink($classId, $hashes, $fullscreen = 1){
        $currentVersion = 2;
        $hash = array_reduce($hashes, function($val, $i){
            	return $val . pack('n', $i);
            }, pack('N', $currentVersion)
            . pack('C', $classId)
            . pack('C', $fullscreen)
        );
        return 'http://www.pathofexile.com/passive-skill-tree/'
            . str_replace(
                array('+', '/')
                , array('-','_')
                , base64_encode($hash)
            );
}

}