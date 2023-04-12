<?php 

class pacientes extends Controller {

	public function init(){		

		$this->pacientes_model = new pacientesModel();
		$this->exames_model = new examesModel();

		//$this->add_js('app/pacientes/assets/pacientes.js');


	}

	public function index_action(){		

		$dados = array();		

		$id_responsavel = $this->_get('paciente');
		if(is_numeric($id_responsavel))
			$dados['responsavel'] = $this->pacientes_model->get($id_responsavel);	

		$this->q = (isset($_GET['q']) ? $_GET['q'] : ""); 

		$this->pagination = new Pagination();	

		$this->pagination->defineQryString("?q=".$this->q);	

		$count = $this->pacientes_model->getSearch($this->q);		

		$this->pagination->link('admin/pacientes/index/page');			

		$this->pagination->setpaginate(count($count), ih_ItemsPerPage, ih_visibleItems, $this->_get('page') );		


		$id_responsavel = $this->_get('paciente');

		if(is_null($id_responsavel)) {
			$dados['list'] = $this->pacientes_model->getSearch($this->q, $this->pagination->getLimit());		
			$this->view('config/list', $dados);	
		} else {
			$dados['list'] = $this->pacientes_model->getSearch($this->q, $this->pagination->getLimit(), $id_responsavel);
			$this->view('config/list_dependentes', $dados);			
		}


			

	}

	public function list_exames(){

		$cod = $this->_get('id');

		$dados = array();		

		$this->pagination = new Pagination();	

		$count = $this->exames_model->getAllExamesPaciente($cod);		

		$this->pagination->link('admin/pacientes/index/page');			

		$this->pagination->setpaginate(count($count), ih_ItemsPerPage, ih_visibleItems, $this->_get('page') );		

		$dados['list'] = $this->exames_model->getAllExamesPaciente($cod, $this->pagination->getLimit());		

		$dados['paciente'] = $this->pacientes_model->getCode($cod);

		

		$this->view('exames_paciente', $dados);		

	}

	public function add(){	


			$id_responsavel = $this->_get('paciente');


			if($this->_post()) {				

				$dataSave = array(	
					
					"nome" => $this->_post("nome"),
					"codigo_paciente" => $this->_post("codigo_paciente"),
					"senha" => rand(100000,999999),
					"data_nascimento" => $this->_post("data_nascimento"),
					"email" => $this->_post("email"),
					"telefone" => $this->_post("telefone"),	
					"celular" => $this->_post("celular"),
					'id_user' => $this->init->user['id_user'],
					'date_created' => date('Y-m-d H:i'),
					'date_update' => date('Y-m-d H:i')
				);

				if (is_numeric($id_responsavel)) 
					$dataSave['id_responsavel'] = $id_responsavel;

				$this->pacientes_model->save($dataSave);				

				$this->message->setMsg('success','Salvo com sucesso.');		
						

			}			

			$dados = array();

			if(is_numeric($id_responsavel))
				$dados['responsavel'] = $this->pacientes_model->get($id_responsavel);			

			$this->view('config/add', $dados);	

	}

	public function details(){

		$id = $this->_get('id');

		$dados = array();

		$dados['data'] = $this->pacientes_model->get($id);

		$this->view('config/details', $dados);

	}

	public function edit(){
		
		$id = $this->_get('id');

		if($this->_post()) {

			$dataSave = array(	

				"nome" => $this->_post("nome"),					
				"codigo_paciente" => $this->_post("codigo_paciente"),	
				"senha" => $this->_post("senha"),	
				"data_nascimento" => $this->_post("data_nascimento"),					
				"email" => $this->_post("email"),					
				"telefone" => $this->_post("telefone"),					
				"celular" => $this->_post("celular"),
				'id_user' => $this->init->user['id_user'],
				'date_update' => date('Y-m-d H:i')				

			);

			$this->pacientes_model->edit($dataSave, 'id_pacientes = '.$id);				

			$this->message->setMsg('success','Editado com sucesso.');					

		}

		$dados = array();
		$dados['data'] = $this->pacientes_model->get($id);

		$this->view('config/edit', $dados);

	}

	public function del(){
		

		if($this->_post('id')){			

			$id = $this->_post('id');			

			$this->pacientes_model->del('id_pacientes = '.$id);

		}

	}


	public function getPacienteAjax() {

		$nameSearch = $this->_post('term');

		$data = $this->pacientes_model->getSearch($nameSearch);

		print json_encode( $data );

	}

	public function addPatient(){

		if($this->_post()) {
			
			$dataSave = array(
				"nome" => $this->_post("namePatient"),
				"data_nascimento" => $this->_post("dataPatient"),
				"cpf" => $this->_post("cpfPatient"),	
				"num_cartao" => $this->_post("numCartaoPatient"),
				"senha" => rand(100000,999999),
				"id_responsavel" => $this->_post("savePatient"),	
				'id_user' => $this->init->user['id_user'],
				'date_created' => date('Y-m-d H:i'),
				'date_update' => date('Y-m-d H:i')							
			);

			$this->pacientes_model->save($dataSave);

			$this->message->setMsg('success','Salvo com sucesso.');			

		}

		$dados = array();	

		$this->view('config/edit', $dados);

	}

}


?>