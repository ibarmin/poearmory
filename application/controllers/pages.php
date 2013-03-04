<?php

class Pages extends CI_Controller {

	public function view($page = 'home') {
        $this->load->helper('security');
        $page = sanitize_filename($page);
		if ( ! file_exists(APPPATH . 'views/pages/' . $page . EXT)){
			show_404();
		}
        $this->output->cache(60 * 24 * 30);
        $data['title'] = ucfirst($page);
		$this->load->view('templates/header', $data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer', $data);
	}
}