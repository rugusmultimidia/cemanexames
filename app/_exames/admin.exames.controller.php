<?php 

class exames extends Controller {

	public function init(){
		
		$this->exames_model 	= new examesModel();
		$this->medicos_model 	= new medicosModel();
		$this->uploder 			= new Upload();

		$this->add_js('app/exames/assets/js/ex.js');

		$this->breadcrumb = array(
			0 => array('icon' => 'home', 'link' => 'admin', 'title' => 'Página Inicial'),
			1 => array('icon' => 'briefcase', 'link' => 'admin/'.$this->controller,	'title' => 'Exames')
		);	

	}


	public function index_action(){		

		$dados = array();	

		$this->pagination = new Pagination();		

		$count = $this->exames_model->count();		

		$this->pagination->link('admin/exames/index/page');			

		$this->pagination->setpaginate($count, ih_ItemsPerPage, ih_visibleItems, $this->_get('page') );		

		$dados['list'] = $this->exames_model->getAll($this->pagination->getLimit());		

		$this->view('list', $dados);		

	}

	

	public function add(){	

			if($this->_post()) {

				$dataSave = array(

					"exame" => $this->_post("exame"),
					"paciente" => $this->_post("paciente"),	
					"codigo_paciente" => $this->_post("codigo_paciente"),
					"email" => $this->_post("email"),
					"data_nascimento" => $this->_post("data_nascimento"),
					"dados" => $this->_post("dados"),
					"login" => rand(100000,999999),
					"senha" => rand(100000,999999),
					"pdf" 	=> $this->_post("pdf_hidden"),
					"assinatura" => $this->_post("assinatura"),
					'id_user' => $this->init->user['id_user'],
					'date_created' => date('Y-m-d H:i'),
					'date_update' => date('Y-m-d H:i')		

				);


				$this->exames_model->save($dataSave);				

				$this->message->setMsg('success','Salvo com sucesso.');					

			}			

			$dados = array();			

			$this->view('add', $dados);

	

	}

	public function edit(){

		$id = $this->_get('id');

		if($this->_post()) {

			$dataSave = array(	 

				"exame" 			=> $this->_post("exame"),
				"paciente"  		=> $this->_post("paciente"),
				"codigo_paciente"   => $this->_post("codigo_paciente"),
				"email" 			=> $this->_post("email"),
				"data_nascimento"   => $this->_post("data_nascimento"),
				"dados" 			=> $this->_post("dados"),
				"login" 			=> $this->_post("login"),
				"senha" 			=> $this->_post("senha"),
				"pdf" 				=> $this->_post("pdf_hidden"),
				"assinatura" => $this->_post("assinatura"),
				'id_user'   		=> $this->init->user['id_user'],
				'date_update' 		=> date('Y-m-d H:i')				

			);


			$this->exames_model->edit($dataSave, 'id_exames = '.$id);				

			$this->message->setMsg('success','Editado com sucesso.');					

		}		

		$dados = array();			

		$dados['data'] = $this->exames_model->get($id);			

		$this->view('edit', $dados);	

	}

	

	public function del(){		

		if($this->_post('id')){			

			$id = $this->_post('id');		

			$pdf = $this->_get('pdf');
			$folder = explode('.pdf', $pdf);
			$folder = $folder[0];

			if(!empty($folder)) {
		
				unlink('themes/files/uploads/'.$pdf);
				$this->del_tree('themes/files/uploads/'.$folder);

			}	

			$this->exames_model->del('id_exames = '.$id);

		}	

	}

	public function doc(){

		$id = $this->_get('id');

		if($this->_post()) {

			$folder = $this->_post('folder');

			//print_r($_POST); DIE;

			$dataSave = array(																

				"dados" => serialize($this->_post("data")),									

			);	

			foreach ($this->_post("data") as $value) {

				$this->generate_image($folder, $value);
			
			}

			$this->exames_model->edit($dataSave, 'id_exames = '.$id);

			$this->message->setMsg('success','Salvo com sucesso.');
		}

		if(!is_numeric($id)){
			exit();
		}

		$dados['data'] = $this->exames_model->get($id);

		if(is_array($dados['data'])){
	
			$folder = explode('.pdf', $dados['data'][0]['pdf']);
			$folder = $folder[0];

			$dados['files'] = scandir(getcwd().'/themes/files/exames/'.$folder);
			unset($dados['files'][0]);
			unset($dados['files'][1]);

			$dados['folder'] = $folder;

			if(!empty($dados['data'][0]['dados'])) {
				$dados['doc'] = unserialize($dados['data'][0]['dados']);
			}

		}

		$dados['medicos'] = $this->medicos_model->getAll();

		$this->view('exame_detalhes', $dados);

	}





	public function generate_image($id, $data) {

		if(!file_exists(getcwd().'/themes/files/resultados/'.$id)){
			mkdir(getcwd().'/themes/files/resultados/'.$id,0777);
		}

		if(!empty($data['assinatura']['img'])) {

			$src = $this->imageCreateFromAny(getcwd().'/themes/files/uploads/'.$data['assinatura']['img']);
			$dest  = $this->imageCreateFromAny(getcwd().'/themes/files/exames/'.$id.'/'.$data['img']);
			imagealphablending($dest, false);
			imagesavealpha($dest, true);
			// Copy and merge
			imagecopymerge($dest, $src, $data['assinatura']['x'], $data['assinatura']['y'], 0, 0, 450, 96, 100);
			//imagecopymerge($dest, $src, posicao x, posicao y, 0, 0, largura_assinatura, altura_assinatura, 100);
			// Output and free from memory
						
			//header('Content-Type: image/png');
			imagepng($dest, getcwd().'/themes/files/resultados/'.$id.'/exame-'.$data['pagina'].'.png');
			imagedestroy($dest);
			imagedestroy($src);

		} else {


			$dest  = $this->imageCreateFromAny(getcwd().'/themes/files/exames/'.$id.'/'.$data['img']);
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

			$filename = $this->uploder->upload();

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

}


?>