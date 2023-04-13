<?php 
require_once('helpers/php-image-magician/php_image_magician.php');

class medicos extends Controller {

	public function init(){		

		$this->medicos_model = new medicosModel();		

		$this->uploder = new Upload();	

		$this->breadcrumb = array(
			0 => array('icon' => 'home', 'link' => 'admin', 'title' => 'Página Inicial'),
			1 => array('icon' => 'briefcase', 'link' => 'admin/'.$this->controller,	'title' => 'Médicos')
		);			

	}

	public function index_action(){		

		$dados = array();	

		$this->pagination = new Pagination();
		$count = $this->medicos_model->count();	
		$this->pagination->link('admin/medicos/index/page');	
		$this->pagination->setpaginate($count, ih_ItemsPerPage, ih_visibleItems, $this->_get('page') );	

		$dados['list'] = $this->medicos_model->getAll($this->pagination->getLimit());		

		$this->view('config/list', $dados);

	}	

	public function add(){	


		include_once 'helpers/imageresize.php';	

			if($this->_post()) {				

				$dataSave = array(

					"nome" => $this->_post("nome"),
					"crm" => $this->_post("crm"),
					"especialidade" => $this->_post("especialidade"),
					'id_user' => $this->init->user['id_user'],
					'date_created' => date('Y-m-d H:i'),
					'date_update' => date('Y-m-d H:i')		

				);				

				if($_FILES["assinatura"]["error"] == 0) {

					//get ext
					$ext = explode('.',$_FILES["assinatura"]['name']);
         			$ext = $ext[1];        


					
				    //$image->load($_FILES["assinatura"]['tmp_name']);

				    

				    //$fileName = md5($_FILES["assinatura"]["name"].time()).'.'.$ext;

				    				

					$this->uploder->setFile($_FILES["assinatura"]);									

					$dataSave["assinatura"] = $this->uploder->upload();//$fileName ;//$this->uploder->upload();


					
					/*$image = new ImageResize('themes/files/uploads/'.$dataSave["assinatura"]);
					$image->resizeToWidth(450);
					$image->save('themes/files/uploads/'.$dataSave["assinatura"]);	*/

					// *** Open JPG image
					$magicianObj = new imageLib('themes/files/uploads/'.$dataSave["assinatura"]);

					// *** Resize to best fit then crop
					$magicianObj -> resizeImage(450, 250, 'landscape');

					// *** Save resized image as a PNG
					$magicianObj -> saveImage('themes/files/uploads/'.$dataSave["assinatura"]);

					if(!file_exists('themes/files/assinaturas/'.$dataSave["assinatura"])) {
						$copia_arquivo = copy('themes/files/uploads/'.$dataSave["assinatura"], 'themes/files/assinaturas/'.$dataSave["assinatura"]);
					}

				}
				

				$this->medicos_model->save($dataSave);

				$this->message->setMsg('success','Salvo com sucesso.');
			}
		

			$dados = array();

			$this->view('config/add', $dados);

	}

	

	public function details(){

		$id = $this->_get('id');	

		$dados = array();

		$dados['data'] = $this->medicos_model->get($id);			

		$this->view('config/details', $dados);

	}

	

	public function edit(){		

		$id = $this->_get('id');	

		//include_once 'helpers/imageresize.php';			

		

		if($this->_post()) {				

			$dataSave = array(	
									

				"nome" => $this->_post("nome"),	
				"crm" => $this->_post("crm"),
				"especialidade" => $this->_post("especialidade"),
				'id_user' => $this->init->user['id_user'],
				'date_update' => date('Y-m-d H:i')				

			);

			if($_FILES["assinatura"]["error"] == 0) {

					//get ext
					$ext = explode('.',$_FILES["assinatura"]['name']);
         			$ext = $ext[1];        


					
				    //$image->load($_FILES["assinatura"]['tmp_name']);

				    

				    //$fileName = md5($_FILES["assinatura"]["name"].time()).'.'.$ext;

				    				

					$this->uploder->setFile($_FILES["assinatura"]);									

					$dataSave["assinatura"] = $this->uploder->upload();//$fileName ;//$this->uploder->upload();


					
					/*$image = new ImageResize('themes/files/uploads/'.$dataSave["assinatura"]);
					$image->resizeToWidth(450);
					$image->save('themes/files/uploads/'.$dataSave["assinatura"]);	*/

					// *** Open JPG image
					$magicianObj = new imageLib('themes/files/uploads/'.$dataSave["assinatura"]);

					// *** Resize to best fit then crop
					$magicianObj -> resizeImage(450, 250, 'landscape');

					// *** Save resized image as a PNG
					$magicianObj -> saveImage('themes/files/uploads/'.$dataSave["assinatura"]);

					if(!file_exists('themes/files/assinaturas/'.$dataSave["assinatura"])) {
						$copia_arquivo = copy('themes/files/uploads/'.$dataSave["assinatura"], 'themes/files/assinaturas/'.$dataSave["assinatura"]);
					}
			}			

			$this->medicos_model->edit($dataSave, 'id_medicos = '.$id);				

			$this->message->setMsg('success','Editado com sucesso.');					

		}		

		$dados = array();

		$dados['data'] = $this->medicos_model->get($id);


		$this->view('config/edit', $dados);	

	}

	

	public function del(){

		if($this->_post('id')){			

			$id = $this->_post('id');			

			$this->medicos_model->del('id_medicos = '.$id);

		}	

	}

}

?>