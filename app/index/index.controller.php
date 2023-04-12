<?php 



class index extends Controller {





	public function init(){



	}





	public function index_action(){

		
		header('location:exames');		

	}

	

	public function ajaxUpload(){

		

		$data = array();

		

		$this->uploder = new Upload();

		

		$this->uploder->setFile($_FILES['file']);

		

		$data['name'] = $this->uploder->upload();

		$data['url'] = 'uploads/'.$data['name'];

		

		print json_encode($data);

				

	}





}



?>