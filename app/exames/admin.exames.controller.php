<?php 

class exames extends Controller {

	public function init(){
		
		$this->exames_model 	= new examesModel();
		$this->medicos_model 	= new medicosModel();
		$this->uploder 			= new Upload();
		$this->pacientes_model = new pacientesModel();

		$this->add_js('app/exames/assets/js/ex.js');

		$this->breadcrumb = array(
			0 => array('icon' => 'home', 'link' => 'admin', 'title' => 'Página Inicial'),
			1 => array('icon' => 'briefcase', 'link' => 'admin/'.$this->controller,	'title' => 'Exames')
		);	

	}


	public function index_action(){		

		$dados = array();	


		$this->add_js('app/exames/assets/js/clipboard.min.js');


		$this->pagination = new Pagination();		

		if(isset($_GET['q'])) {
			$this->q = $_GET['q'];
		} else {
			$this->q ="";
		}

		$count = $this->exames_model->getAllByPacienteName($this->q);	

		$this->pagination->link('admin/exames/index/page');			

		$this->pagination->setpaginate(count($count), ih_ItemsPerPage, ih_visibleItems, $this->_get('page') );		

		$dados['list'] = $this->exames_model->getAllByPacienteName($this->q, $this->pagination->getLimit());		
		// $this->printar($dados);

		$this->view('list', $dados);		

	}


	public function exames_new(){		

		$dados = array();	


		$this->add_js('app/exames/assets/js/clipboard.min.js');


		$this->pagination = new Pagination();		

		if(isset($_GET['q'])) {
			$this->q = $_GET['q'];
		} else {
			$this->q ="";
		}

		$queryString = $this->pagination->extrairQueryString($_SERVER['QUERY_STRING']);
		$this->pagination->defineQryString($queryString);

		$count = $this->exames_model->getAllByPacienteName($this->q);	

		$this->pagination->link('admin/exames/exames_new/page');			

		$this->pagination->setpaginate(count($count), ih_ItemsPerPage, ih_visibleItems, $this->_get('page') );		

		$dados['list'] = $this->exames_model->getAllByPacienteName($this->q, $this->pagination->getLimit());		
		// $this->printar($dados);

		$this->view('list_new', $dados);		

	}

	

	public function create(){	

			if($this->_post("novo_exame")) {
				
				$dados = array();			

				$dados['paciente'] = $this->pacientes_model->get($this->_post("id_paciente"))[0];	

				$this->view('create', $dados);

			} elseif ($this->_post("cria_exame")){

				// $this->printar($_POST);

				$filesPDF = array();

				if(isset($_FILES['pdf'])) {

					$size = count($_FILES['pdf']['size']);

					for ($i=0; $i < $size; $i++) { 
							

						if($_FILES['pdf']['error'][$i] == 0){

							///$this->uploder->setFile($_FILES[0]);	
							$filesPDF[$i]['name'] 		= $_FILES['pdf']['name'][$i];
							$filesPDF[$i]['type'] 		= $_FILES['pdf']['type'][$i];
							$filesPDF[$i]['tmp_name'] 	= $_FILES['pdf']['tmp_name'][$i];
							$filesPDF[$i]['error'] 		= $_FILES['pdf']['error'][$i];
							// $this->uploder->upload();
						}

					}

				}


				//print_r($_FILES);die;
				if(isset($_FILES['imagem'])) {

					$size = count($_FILES['imagem']['size']);

					for ($i=0; $i < $size; $i++) { 
							

						if($_FILES['imagem']['error'][$i] == 0){

							///$this->uploder->setFile($_FILES[0]);	
							$filesImg[$i]['name'] 		= $_FILES['imagem']['name'][$i];
							$filesImg[$i]['type'] 		= $_FILES['imagem']['type'][$i];
							$filesImg[$i]['tmp_name'] 	= $_FILES['imagem']['tmp_name'][$i];
							$filesImg[$i]['error'] 		= $_FILES['imagem']['error'][$i];
							// $this->uploder->upload();
						}

					}

				}

				$filesNames = array();

				$i = 0;
				foreach ($filesPDF as $pdf) {
					
					$this->uploder->setFile($pdf);	
					$filesNames[$i]['file'] = $this->uploder->_upload();
					$i++;
				}

				$ImageNames = array();

				$i = 0;
				foreach ($filesImg as $img) {
					
					$this->uploder->setFile($img);	
					$ImageNames[0]['imagem'][] = $this->uploder->_upload();
				}

				

				if(isset($_POST['assinatura'])) {
					$assinatura = $this->_post("assinatura");
				} else {
					$assinatura = 0;
				}


				/*----------------------------------------
					Cadastra o exame
				----------------------------------------*/

				$dataSave = array(
					"exame" => html_entity_decode($this->_post("exame"), ENT_QUOTES, 'UTF-8'),
					"paciente" => html_entity_decode($this->_post("paciente"), ENT_QUOTES, 'UTF-8'),
					"codigo_paciente" => html_entity_decode($this->_post("codigo_paciente"), ENT_QUOTES, 'UTF-8'),
					"email" => html_entity_decode($this->_post("email"), ENT_QUOTES, 'UTF-8'),
					"data_nascimento" => html_entity_decode($this->_post("data_nascimento"), ENT_QUOTES, 'UTF-8'),
					"dados" => html_entity_decode($this->_post("dados"), ENT_QUOTES, 'UTF-8'),
					"cpf" => $this->cleanCPF(html_entity_decode($this->_post("cpf"), ENT_QUOTES, 'UTF-8')),
					"id_pacientes" => html_entity_decode($this->_post("id_pacientes"), ENT_QUOTES, 'UTF-8'),
					"pdf" => serialize($filesNames),
					"imagem" => serialize($ImageNames),
					"clinica" => $this->clinica(),
					"assinatura" => $assinatura,
					'id_user' => $this->init->user['id_user'],
					'date_created' => date('Y-m-d H:i'),
					'date_update' => date('Y-m-d H:i')
				);

				if (empty($dataSave['codigo_paciente'])) {
					$dataSave['codigo_paciente'] = 0;
				}

				$this->exames_model->save($dataSave);	
				$this->message->setMsg('success','Salvo com sucesso.');	
			}

			header('Location: /admin/exames/exames_new?q='.$this->_post("cpf"));

	}




	public function add(){	

			if($this->_post()) {


				$filesPDF = array();



				if(isset($_FILES['pdf'])) {

					$size = count($_FILES['pdf']['size']);

					for ($i=0; $i < $size; $i++) { 
							

						if($_FILES['pdf']['error'][$i] == 0){

							///$this->uploder->setFile($_FILES[0]);	
							$filesPDF[$i]['name'] 		= $_FILES['pdf']['name'][$i];
							$filesPDF[$i]['type'] 		= $_FILES['pdf']['type'][$i];
							$filesPDF[$i]['tmp_name'] 	= $_FILES['pdf']['tmp_name'][$i];
							$filesPDF[$i]['error'] 		= $_FILES['pdf']['error'][$i];
							// $this->uploder->upload();
						}

					}

				}


				//print_r($_FILES);die;
				if(isset($_FILES['imagem'])) {

					$size = count($_FILES['imagem']['size']);

					for ($i=0; $i < $size; $i++) { 
							

						if($_FILES['imagem']['error'][$i] == 0){

							///$this->uploder->setFile($_FILES[0]);	
							$filesImg[$i]['name'] 		= $_FILES['imagem']['name'][$i];
							$filesImg[$i]['type'] 		= $_FILES['imagem']['type'][$i];
							$filesImg[$i]['tmp_name'] 	= $_FILES['imagem']['tmp_name'][$i];
							$filesImg[$i]['error'] 		= $_FILES['imagem']['error'][$i];
							// $this->uploder->upload();
						}

					}

				}

				$filesNames = array();

				$i = 0;
				foreach ($filesPDF as $pdf) {
					
					$this->uploder->setFile($pdf);	
					$filesNames[$i]['file'] = $this->uploder->_upload();
					$i++;
				}

				$ImageNames = array();

				$i = 0;
				foreach ($filesImg as $img) {
					
					$this->uploder->setFile($img);	
					$ImageNames[0]['imagem'][] = $this->uploder->_upload();
				}

				

				if(isset($_POST['assinatura'])) {
					$assinatura = $this->_post("assinatura");
				} else {
					$assinatura = 0;
				}


				/*----------------------------------------
					Cadastra o exame
				----------------------------------------*/

				$dataSave = array(

					"exame" => $this->_post("exame"),
					"paciente" => $this->_post("paciente"),	
					"codigo_paciente" => $this->_post("codigo_paciente"),
					"email" => $this->_post("email"),
					"data_nascimento" => $this->_post("data_nascimento"),
					"dados" => $this->_post("dados"),
					"pdf" 	=> serialize($filesNames),
					"imagem" 	=> serialize($ImageNames),
					"clinica" => $this->clinica(),
					"assinatura" => $assinatura,
					'id_user' => $this->init->user['id_user'],
					'date_created' => date('Y-m-d H:i'),
					'date_update' => date('Y-m-d H:i')		

				);


				$this->exames_model->save($dataSave);	



				/*----------------------------------------
					Verifica se existe o paciente, 
					se nao cadastra um novo
				----------------------------------------*/

				$codigo_paciente = $this->_post("codigo_paciente");

				$check_code = $this->pacientes_model->getCode($codigo_paciente);

				if(!isset($check_code[0])) {


					$dataSave = array(	
						"nome" => $this->_post("paciente"),
						"codigo_paciente" => $this->_post("codigo_paciente"),
						"senha" => rand(100000,999999),
						"data_nascimento" => $this->_post("data_nascimento"),
						"email" => $this->_post("email"),
						'id_user' => $this->init->user['id_user'],
						'date_created' => date('Y-m-d H:i'),
						'date_update' => date('Y-m-d H:i')
					);

					$this->pacientes_model->save($dataSave);	

				}


				$this->message->setMsg('success','Salvo com sucesso.');					

			}			

			$dados = array();			

			$this->view('add', $dados);

	

	}

	public function edit_new() {
		$id = $this->_get('id');

		
	
		if ($this->_post()) {

			// $this->printar($_POST);
			// POST das imagens
			$filesImg = array();
			$old_image = array();
	
			if (isset($_POST['active_image'])) {
				$old_image = $_POST['active_image'];
			}
	
			$filesImages = array();
	
			if (isset($_FILES['imagem'])) {
				foreach ($_FILES['imagem'] as $imgkey => $imgvalue) {
					$i = 0;
					foreach ($imgvalue as $pdf => $value) {
						foreach ($value as $key => $value) {
							$filesImages[$pdf][$key][$imgkey] = $value;
						}
					}
					$i++;
				}
	
				// Unset errors
				$size = count($filesImages);
				for ($i = 0; $i < $size; $i++) {
					$size_inside = count($filesImages[$i]);
					for ($j = 0; $j < $size_inside; $j++) {
						if ($filesImages[$i][$j]['error'] != 0) {
							unset($filesImages[$i]);
						}
					}
				}
			}
	
			$ImageNames = array();
			foreach ($filesImages as $key => $img) {
				foreach ($img as $value) {
					$this->uploder->setFile($value);
					$ImageNames[$key]['imagem'][] = $this->uploder->_upload();
				}
			}
	
			// Merge imagens antigas com as novas
			$result2 = array();
			foreach ($old_image as $k => $v) {
				foreach ($v['imagem'] as $key => $value) {
					$result2[$k]['imagem'][$key] = $value;
				}
			}
			foreach ($ImageNames as $k => $v) {
				foreach ($v['imagem'] as $key => $value) {
					$result2[$k]['imagem'][] = $value;
				}
			}
	
			// POST dos PDFS
			$old_pdf = array();
			if (isset($_POST['active_file'])) {
				$old_pdf = $_POST['active_file'];
			}
	
			$filesPDF = array();
			if (isset($_FILES['pdf'])) {
				$size = count($_FILES['pdf']['size']);
				for ($i = 0; $i < $size; $i++) {
					if ($_FILES['pdf']['error'][$i] == 0) {
						$filesPDF[$i]['name'] = $_FILES['pdf']['name'][$i];
						$filesPDF[$i]['type'] = $_FILES['pdf']['type'][$i];
						$filesPDF[$i]['tmp_name'] = $_FILES['pdf']['tmp_name'][$i];
						$filesPDF[$i]['error'] = $_FILES['pdf']['error'][$i];
					}
				}
			}
	
			$filesNames = array();
			$i = 0;
			foreach ($filesPDF as $pdf) {
				$this->uploder->setFile($pdf);
				$filesNames[$i]['file'] = $this->uploder->_upload();
				if (!$filesNames[$i]['file']) {
					die("Arquivo não foi gravado.");
				}
				$i++;
			}
	
			$result1 = array_merge($filesNames, $old_pdf);
	
			if ($id == 30548) {
				// Debugging code
			}
	
			if (isset($_POST['assinatura'])) {
				$assinatura = $this->_post("assinatura");
			} else {
				$assinatura = 0;
			}
	
			$dataSave = array(
				"exame" => $this->_post("exame"),
				"pdf" => serialize($result1),
				"imagem" => serialize($result2),
				"assinatura" => $assinatura,
				'id_user' => $this->init->user['id_user'],
				'date_update' => date('Y-m-d H:i')
			);

			if($this->master()){
				$dataSave['id_pacientes'] = $this->_post("id_pacientes_admin");
				$dataSave['codigo_paciente'] = $this->_post("codigo_paciente_admin");
				$dataSave['data_nascimento'] = date('Y-m-d', strtotime(str_replace('/', '-', $this->_post("data_nascimento_admin"))));
				$dataSave['paciente'] = $this->_post("paciente_admin");
				$dataSave['cpf'] = $this->cleanCPF($this->_post("cpf_admin"));
				$dataSave['clinica'] = $this->_post("clinica_admin");
			}

			// $this->printar($dataSave);
	
			$this->exames_model->edit($dataSave, 'id_exames = ' . $id);
			$this->message->setMsg('success', 'Editado com sucesso.');
		}
	
		$dados = array();
		$dados['data'] = $this->exames_model->get($id);
		// $this->printar($dados['data']);

		$dados['paciente'] = $this->exames_model->getPacienteByNome($dados['data'][0]['paciente'], $dados['data'][0]['data_nascimento'])[0];
		// $this->printar($dados['paciente']);
		
		$dados['pdfs'] = unserialize($dados['data'][0]['pdf']);
	
		if (count($dados['pdfs']) == 0) {
			$dados['pdfs'] = array();
		}
	
		$dados['images'] = unserialize($dados['data'][0]['imagem']);
	
		if (!is_array($dados['images']) || count($dados['images']) == 0) {
			$dados['images'] = array();
		}
	
		if (@$_GET['debug']) {
			print_r($dados);
			die;
		}

		// $this->printar($dados);
	
		$this->view('edit_new', $dados);
	}


	public function edit(){


		die('erro no servidor');

		$id = $this->_get('id');




		if($this->_post()) {

			// print_r($_FILES); die;

			

			
			
			

			/*------------------------------
					POST das imagens
			------------------------------*/

			$filesImg = array();
			$old_image = array();

			if(isset($_POST['active_image'])) {
				$old_image = $_POST['active_image'];
			}

			$filesImages = array();

			if(isset($_FILES['imagem'])) {

				foreach ($_FILES['imagem'] as $imgkey => $imgvalue) {
					
					//2x
					$i = 0;
					foreach ($imgvalue as $pdf => $value) {
						
						foreach ($value as $key => $value) {

							$filesImages[$pdf][$key][$imgkey] = $value;	
						}

					}

					$i++;
				}

				//unset errors

				$size = count($filesImages);

				for ($i=0; $i < $size; $i++) {

					$size_inside = count($filesImages[$i]);

					for ($j=0; $j < $size_inside; $j++) { 

						if($filesImages[$i][$j]['error'] != 0) {
							unset($filesImages[$i]);
						}
					}


				}


			}

			$ImageNames = array();
			
			foreach ($filesImages as $key => $img) {

				foreach ($img as $value) {
					$this->uploder->setFile($value);
					$ImageNames[$key]['imagem'][] = $this->uploder->_upload();
					
				}
			}

			//Merge imagens antigas com as novas
			$result2 = array();
			
			foreach ($old_image as $k => $v) {
				
				foreach ($v['imagem'] as $key => $value) {
					$result2[$k]['imagem'][$key] = $value;
				}
			}
			foreach ($ImageNames as $k => $v) {
				
				foreach ($v['imagem'] as $key => $value) {
					$result2[$k]['imagem'][] = $value;
				}
			}



			/*------------------------------
					POST dos PDFS
			------------------------------*/

			$old_pdf = array();

			if(isset($_POST['active_file'])) {
				$old_pdf = $_POST['active_file'];
			} 
			
			$filesPDF = array();

			//print_r($_FILES['pdf']); die;

			if(isset($_FILES['pdf'])) {

				$size = count($_FILES['pdf']['size']);

				for ($i=0; $i < $size; $i++) { 
						

					if($_FILES['pdf']['error'][$i] == 0){

						///$this->uploder->setFile($_FILES[0]);	
						$filesPDF[$i]['name'] 		= $_FILES['pdf']['name'][$i];
						$filesPDF[$i]['type'] 		= $_FILES['pdf']['type'][$i];
						$filesPDF[$i]['tmp_name'] 	= $_FILES['pdf']['tmp_name'][$i];
						$filesPDF[$i]['error'] 		= $_FILES['pdf']['error'][$i];
						// $this->uploder->upload();
					}

				}

			}

			$filesNames = array();

			$i = 0;
			foreach ($filesPDF as $pdf) {
				
				$this->uploder->setFile($pdf);
				$filesNames[$i]['file'] = $this->uploder->_upload();
				if (!$filesNames[$i]['file']){
					die("Arquivo não foi gravado.");
				}
				//$filesNames[$i]['images'] = 
				$i++;
			}

			$result1 = array_merge($filesNames, $old_pdf);
			
			if($id == 2472) {
				//print_r($ImageNames);
				//print_r($old_image);
				//print_r($result2);
				//die;				//print_r($ImageNames); 
				//die;
			}

			if(isset($_POST['assinatura'])) {
				$assinatura = $this->_post("assinatura");
			} else {
				$assinatura = 0;
			}

			$dataSave = array(	 

				"exame" 			=> $this->_post("exame"),
				"paciente"  		=> $this->_post("paciente"),
				"codigo_paciente"   => $this->_post("codigo_paciente"),
				"email" 			=> $this->_post("email"),
				"data_nascimento"   => $this->_post("data_nascimento"),
				"dados" 			=> $this->_post("dados"),
				"pdf" 				=> serialize($result1),
				"imagem" 			=> serialize($result2),
				"assinatura" 		=> $assinatura,
				'id_user'   		=> $this->init->user['id_user'],
				'date_update' 		=> date('Y-m-d H:i')				

			);


			$this->exames_model->edit($dataSave, 'id_exames = '.$id);				

			$this->message->setMsg('success','Editado com sucesso.');					

		}		

		$dados = array();			

		$dados['data'] = $this->exames_model->get($id);	

		$dados['pdfs'] = unserialize($dados['data'][0]['pdf']);	

		//print_r($dados['pdfs']);

		if(count($dados['pdfs']) == 0) {
			$dados['pdfs'] = array();
		}

		$dados['images'] = unserialize($dados['data'][0]['imagem']);	



		if(!is_array($dados['images']) || count($dados['images']) == 0) {
			$dados['images'] = array();
		}
		//print_r($dados['images']); die;
		//print_r($dados['pdfs']); die;



		if(@$_GET['debug']){
			print_r($dados); die;
		}
		$this->view('edit', $dados);	

	}

	

	public function del_(){		


		if($this->_post('id')){			

			$id = $this->_post('id');		

			$pdf = $this->_get('pdf');
			$folder = explode('.pdf', $pdf);
			$folder = $folder[0];

			if(!empty($folder) && !empty($pdf)) {

				if(file_exists('themes/files/uploads/'.$pdf)){
					unlink('themes/files/uploads/'.$pdf);
				}		
				
				if(is_dir('themes/files/uploads/'.$folder)){
					$this->del_tree($dirToDel);
				}
				
			}	

			$this->exames_model->del('id_exames = '.$id);

		}	

	}

	public function del(){		

		if($this->_post('id')){			

			$id = $this->_post('id');		

			$dataSave = array(
				"ativo" => "apagado"
			);

			$this->exames_model->edit($dataSave, 'id_exames = '.$id);

		}	

	}

	public function doc(){

		// phpinfo();die;

		$id = $this->_get('id');
		$file = $this->_get('file');

		$folder = explode('.pdf', $file);
		$this->folder = $folder[0];

		// print_r($this->folder); die;

		if($this->_post()) {

			// $this->printar($_POST);
			
			// print_r($_POST); die;
			// include_once 'helpers/mpdf_v6/mpdf.php';
			include_once 'helpers/vendor/mpdf/mpdf/mpdf.php';

			$mpdf = new mPDF(); 
			// print_r($_POST['data'][$this->folder]); die;
			$i = 0;
			foreach ($_POST['data'][$this->folder] as $value) {
			
				$this->generate_image($this->folder, $value);
			
				// Determine image orientation
				$imagePath = "themes/files/resultados/".$this->folder."/exame-".$i.".png";
				if (file_exists($imagePath)) {
					list($width, $height) = getimagesize($imagePath);
					$orientation = ($width > $height) ? 'L' : 'P'; // L = Landscape, P = Portrait
					// Add a new page with the correct orientation
					$mpdf->AddPage($orientation);
			
					$mpdf->WriteHTML("<div><img src='" . $imagePath . "'></div>");
					$i++;
				}
			
				// die("manutencao");
			
			}

			//pega os dados ja gravados anteriormente.
			if(empty($_POST['dataOld'])) {
				$dataOld = array();
			} else {
				$dataOld = unserialize($_POST['dataOld']);
			}
			//print_r($_POST['data']);
			//print_r($dataOld);
			$result = array_merge($dataOld, $_POST['data']);
			//print_r($result); die;
			//print_r($result);

			$dataSave = array(																

				"dados" => serialize($result),									

			);	

			//save o pdf com a assinatura.
			$mpdf->SetDisplayMode('fullpage');		
			$mpdf->Output('themes/files/uploads/'.$this->folder.'.pdf');

			//$mpdf->Output('themes/files/uploads/teste.pdf');



			$this->exames_model->edit($dataSave, 'id_exames = '.$id);

			$this->message->setMsg('success','Salvo com sucesso.');
		}



		$dados['data'] = $this->exames_model->get($id);

		// print_r($dados['data']); die;

		if(file_exists(getcwd().'/themes/files/exames/'.$this->folder)){

			$dados['files'] = scandir(getcwd().'/themes/files/exames/'.$this->folder);
			unset($dados['files'][0]);
			unset($dados['files'][1]);

			$dados['arquivos'] = array();
			foreach ($dados['files'] as $file) {
				$filePath = getcwd() . '/themes/files/exames/' . $this->folder . '/' . $file;
				if (is_file($filePath)) {
					$size = getimagesize($filePath);
					if ($size) {
						$orientation = ($size[0] > $size[1]) ? 'horizontal' : 'vertical';
						$dados['arquivos'][] = array(
							'arquivo' => $file,
							'orientacao' => $orientation
						);
					}
				}
			}

		} else {
			print 'Arquivo não exite, verifique se o mesmo foi convertido.';
			exit;
		}

		if(is_array($dados['data'])){


			if(!empty($dados['data'][0]['dados'])) {
				$dados['doc'] = unserialize($dados['data'][0]['dados']);

				
			}

		}

		$dados['medicos'] = $this->medicos_model->getAll();

		$dados['pdf'] = $data[0]['pdf'];
		$dados['pdf'] = unserialize($dados['data'][0]['pdf']);
		// $this->printar($dados);
		

		$this->view('exame_detalhes', $dados);

	}

	public function teste () {

		require_once('helpers/php-image-magician/php_image_magician.php');

		$value = array(
				'pagina' => '0', 
				'img' => 'exame-0.png', 
				'assinatura' => Array
	                (
	                    'img' => '461d7bf599cae871446d307919a6ba63.png',
	                    'y' => 79,
	                    'x' => 51
	                )
	    );



		$this->generate_image('opa', $value);
	}



	public function generate_image($id, $data) {

		if(!file_exists(getcwd().'/themes/files/resultados/'.$id)){
			mkdir(getcwd().'/themes/files/resultados/'.$id,0777);
		}

		if(!empty($data['assinatura']['img'])) {


			/* original
			$src = $this->imageCreateFromAny(getcwd().'/themes/files/uploads/'.$data['assinatura']['img']);
			$dest  = $this->imageCreateFromAny(getcwd().'/themes/files/exames/'.$id.'/'.$data['img']);
			
			$white = imagecolorallocate($dest, 255, 255, 255); 
			imagecolortransparent($dest, $white);

			$size = getimagesize(getcwd().'/themes/files/uploads/'.$data['assinatura']['img']);

			imagealphablending($dest, false);
			imagesavealpha($dest, true);
			imagecopymerge($dest, $src, $data['assinatura']['x'], $data['assinatura']['y'], 0, 0, $size[0], $size[1], 100);
			//imagecopymerge($dest, $src, posicao x, posicao y, 0, 0, largura_assinatura, altura_assinatura, 100);
			//header('Content-Type: image/png');

			imagepng($dest, getcwd().'/themes/files/resultados/'.$id.'/exame-'.$data['pagina'].'.jpg');
			imagedestroy($dest);
			//imagedestroy($src);
			*/

			$src = $this->imageCreateFromAny(getcwd().'/themes/files/uploads/'.$data['assinatura']['img']);
			$dest  = $this->imageCreateFromAny(getcwd().'/themes/files/exames/'.$id.'/'.$data['img']);
			
			//$white = imagecolorallocate($dest, 255, 255, 255); 
			//imagecolortransparent($dest, $white);

			$size = getimagesize(getcwd().'/themes/files/uploads/'.$data['assinatura']['img']);

			//imagealphablending($dest, false);
			imagesavealpha($dest, true);

			//create a fully transparent background (127 means fully transparent)
            $trans_background = imagecolorallocatealpha($dest, 0, 0, 0, 127);

            //fill the image with a transparent background
            imagefill($dest, 0, 0, $trans_background);

			imagecopy($dest, $src, $data['assinatura']['x'], $data['assinatura']['y'], 0, 0, $size[0], $size[1]);
			//imagecopymerge($dest, $src, posicao x, posicao y, 0, 0, largura_assinatura, altura_assinatura, 100);
			//header('Content-Type: image/png');

			imagepng($dest, getcwd().'/themes/files/resultados/'.$id.'/exame-'.$data['pagina'].'.png');
			imagedestroy($dest);
			//imagedestroy($src);

		} else {


			$dest  = $this->imageCreateFromAny(getcwd().'/themes/files/exames/'.$id.'/'.$data['img']);
			$white = imagecolorallocate($dest, 255, 255, 255); 
			imagecolortransparent($dest, $white);

			imagealphablending($dest, false);
			imagesavealpha($dest, true);
			imagepng($dest, getcwd().'/themes/files/resultados/'.$id.'/exame-'.$data['pagina'].'.png');
			imagedestroy($dest);
			/*$url="http://www.google.co.in/intl/en_com/images/srpr/logo1w.png";
			$contents=file_get_contents($url);
			$save_path="/path/to/the/dir/and/image.jpg";
			file_put_contents($save_path,$contents);*/


		}
	} 



	public function fileupload(){				

		if($_FILES[0]["error"] == 0) {					

			$this->uploder->setFile($_FILES[0]);									

			$filename = $this->uploder->_upload();

			$data = array('success' => 'success', 'filename' => $filename);

			print json_encode($data);

		}			

	}


	
	public function del_tree($dir) { 
	   $files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) { 
		  (is_dir("$dir/$file")) ? $this->del_tree("$dir/$file") : unlink("$dir/$file"); 
		} 
		return rmdir($dir); 
  	}



  	function imageCreateFromAny($filepath) { 
	    $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize() 
	    $allowedTypes = array( 
	        1,  // [] gif 
	        2,  // [] jpg 
	        3,  // [] png 
	        6   // [] bmp 
	    ); 
	    if (!in_array($type, $allowedTypes)) { 
	        return false; 
	    } 
	    switch ($type) { 
	        case 1 : 
	            $im = imageCreateFromGif($filepath); 
	        break; 
	        case 2 : 
	            $im = imageCreateFromJpeg($filepath); 
	        break; 
	        case 3 : 
	            $im = imageCreateFromPng($filepath); 
	        break; 
	        case 6 : 
	            $im = imageCreateFromBmp($filepath); 
	        break; 
	    }    
	    return $im;  
	} 

	function resize_image($file, $w, $h, $crop=FALSE) {
	    list($width, $height) = getimagesize($file);
	    $r = $width / $height;
	    if ($crop) {
	        if ($width > $height) {
	            $width = ceil($width-($width*abs($r-$w/$h)));
	        } else {
	            $height = ceil($height-($height*abs($r-$w/$h)));
	        }
	        $newwidth = $w;
	        $newheight = $h;
	    } else {
	        if ($w/$h > $r) {
	            $newwidth = $h*$r;
	            $newheight = $h;
	        } else {
	            $newheight = $w/$r;
	            $newwidth = $w;
	        }
	    }
	    $src = $this->imageCreateFromAny($file);
	    $dst = imagecreatetruecolor($newwidth, $newheight);
	    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

	    return $dst;
	}

	public function ConvertPDF() {

		var_dump(@$_POST);

		$fotos = json_decode(@$_POST['fotos']);
		

		$location = "themes/files/";


		if(isset($_POST['pdf'])){   


		    $pdf = $_POST['pdf'];
		    $folder = explode('.pdf', $pdf);
		    $folder = $folder[0];   

			$filename = $location."uploads/".$pdf;

			// faz uma copia do arquivo original para a pasta /original do SERVIdor
			if(!file_exists($location.'original/'.$pdf)) {
				$copia_arquivo = copy($filename, $location.'original/'.$pdf);
		    }

		    if(!file_exists($location.'exames/'.$folder)) {
		    	mkdir($location.'exames/'.$folder, 0775);
		    }

			// die();

			// var_dump($fotos);
			// $this->printar($fotos);



		    /*---------------------------------------				
					Move as fotos cadastradas
		    -----------------------------------------*/

		    foreach ($fotos as $key => $value) {
		    	
		    	$image  = "themes/files/uploads/".$value;
		    	$imageD = "themes/files/exames/".$folder."/z-exame-".$key.".png";

		    	rename($image, $imageD);

		    }


			//https://stackoverflow.com/questions/58623596/failed-to-get-imagick-load-for-php7-4 para ajustar a biblioteca
			//https://stackoverflow.com/questions/37599727/php-imagickexception-not-authorized solucao para o pdf gravar
		    
		    $imagick = new Imagick();

			$imagick->setResolution(150, 150);

		    $imagick->readImage($filename);

			// Ajusta a resolução com base na orientação da imagem
			$orientation = $imagick->getImageOrientation();

			$width = $imagick->getImageWidth();
			$height = $imagick->getImageHeight();

			// die("Largura: $width, Altura: $height, Orientação: $orientation");

			// Se a imagem for retrato (portrait), usa 1240x1724. Se for paisagem (landscape), usa 1724x1240.
			if ($width > $height) {
				// Paisagem
				$imagick->resizeImage(1724, 1240, Imagick::FILTER_POINT, 1);
			} else {
				// Retrato
				$imagick->resizeImage(1240, 1724, Imagick::FILTER_POINT, 1);
			}
			// Writes an image or image sequence Example- converted-0.jpg, converted-1.jpg
			$imagick->writeImages($location."exames/".$folder."/exame.png", false);




/*
		    $img = new Imagick($location."uploads/".$pdf);



		    $num_pages = $img->getNumberImages();


		    // Convert PDF pages to images
		    for($i = 0;$i < $num_pages; $i++) {   


		    	//$file = new imagick($location."uploads/".$pdf."[".$i."]");

		    	//$file->setImageCompression($compression_type); 
		    	//$file->setImageCompressionQuality(100);
		    	//$file->adaptiveResizeImage(null,1724);
				//$file->setImageBackgroundColor('white');
				//$file->setResolution( 300, 300 );
				//$img->scaleImage(800,0);
				//$file->setImageFormat( "png" );
				//$file->writeImage('newfilename.png');
				      

		        // Set image format
		        $file->setImageFormat('jpg');

		        // Write Images to temp 'upload' folder     
		        $file->writeImage($location."exames/".$folder."/exame_".$i.".png");

		        $file->destroy();
		    }

		    */
		}

		print json_encode(array('success' => true));



	}


	public function ConvertImage() {


		$location = "themes/files/";


		if(isset($_GET['id'])){  

			$data = $this->exames_model->get($_GET['id']);	

			$images = unserialize($data[0]['imagem']);

			//print_r($images); die;

			include_once 'helpers/mpdf_v6/mpdf.php';

			$mpdf = new mPDF();  

			foreach ($images as $value) {
				

				if(file_exists("themes/files/uploads/".$value['file'])) {
					$mpdf->WriteHTML("<div style='padding:20px'><img src='themes/files/uploads/".$value['file']."'></div>");
					//print "themes/files/resultados/".$this->folder."/exame-".$i.".jpg";
					$i++;
				}
				
			
			}

			$mpdf->Output();
		}

		//print json_encode(array('success' => true));



	}

	public function testaDias($date_created, $dias) {

		$date = new DateTime($date_created);
		$date->add(new DateInterval('P' . $dias . 'D'));
		
		$now = new DateTime();
		if ($date > $now) {
			return true;
		} else {
			return false;
		}
		
	}


	private function isDateBR($date) {
		$d = DateTime::createFromFormat('d/m/Y', $date);
		return $d && $d->format('d/m/Y') === $date;
	}

	private function convertDateToUSA($date) {
		$d = DateTime::createFromFormat('d/m/Y', $date);
		return $d->format('Y-m-d');
	}

	public function convertDateFormats() {

		
		for ($i = 0; $i < 100; $i++) {

			echo "loop $i<br>";

			// die("Iniciando...");
			
			$exames = $this->exames_model->getAllNull();
			// $this->printar($exames);
			
			foreach ($exames as $exame) {
	
				$data_nascimento = $exame['data_nascimento'];
	
				// $this->printar($exame);
	
				if ($this->isDateBR($data_nascimento)) {
					$data_nascimento = $this->convertDateToUSA($data_nascimento);
				}
	
				$dataSave = array(
					"data_nascimento" => $data_nascimento,
					'date_update' => date('Y-m-d H:i')
				);
				
				if(empty($exame['id_pacientes'])) {
					// die("Paciente não encontrado: " . $exame['codigo_paciente'] . ' - ' . $exame['paciente']);
					$paciente = $this->pacientes_model->getCode($exame['codigo_paciente'], $exame['paciente'])[0];
					
					if(!empty($paciente)) {
						
						// $this->printar($paciente);
						
						if (!is_int($paciente['id_pacientes']) && !empty($paciente['id_pacientes'])) {
							
							$dataSave['id_pacientes'] = (int)$paciente['id_pacientes'];
							
							echo $exame['paciente'] ." ".$exame['id_exames'] . ' - ' . $exame['data_nascimento'] . ' - ' . $data_nascimento . '<br>';
				
							$this->exames_model->edit($dataSave, 'id_exames = ' . $exame['id_exames']);
						}
					}
				}
				
			}
			
		}

		die("Fim");
	}


}


?>
