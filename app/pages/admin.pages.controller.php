<?php 

class pages extends Controller {
	


	public function init(){
		
		

	}


	public function index_action(){
		
	}
	
	
	public function add(){
		
		
		$this->view('add');
	}
	
	
	public function loadPage() {
		
		$data['page'] = $this->content;
		
		$this->view('page', $data, true);
		
	}

	
	public function not_found(){
		
		$this->view('404', null, true);
		
	}

	


}

?>