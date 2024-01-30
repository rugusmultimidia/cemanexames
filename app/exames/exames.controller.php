<?php 



class exames extends Controller {


	public function init(){		

		$this->exames_model = new examesModel();

	}

	public function index_action(){		

		/*
		if(isset($this->init->user['pdf'])) {

			$folder = explode('.pdf', $this->init->user['pdf'] );
			$folder = $folder[0];
			$dados['folder'] = $folder;

			$data = $this->init->user['dados'];

			//$dados['files'] = unserialize($this->init->user['dados']);

			if(file_exists(getcwd().'/themes/files/resultados/'.$folder)) {

				$dados['files'] = scandir(getcwd().'/themes/files/resultados/'.$folder);
				unset($dados['files'][0]);
				unset($dados['files'][1]);

			} else {
				$dados['files'] = 'Exame incompleto';
			}

		}
		*/

		$dados['list'] = $this->exames_model->getAllExamesPaciente($this->init->user['codigo_paciente']);	
		// $this->printar($_SESSION);
		
		$this->viewNoBase('exame_paciente', $dados);	 	

	}	

	





}



?>