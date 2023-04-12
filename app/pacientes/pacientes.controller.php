<?php 

class pacientes extends Controller {


	public function init(){
		
		$this->pacientes_model = new pacientesModel();
		
		
				
	}


	public function index_action(){
		
		$dados = array();
		
		$this->pagination = new Pagination();
		
		$count = $this->pacientes_model->count();		
		$this->pagination->link('admin/pacientes/index/page');			
		$this->pagination->setpaginate($count, ih_ItemsPerPage, ih_visibleItems, $this->_get('page') );
		
		$dados['list'] = $this->pacientes_model->getAll($this->pagination->getLimit());
		
		$this->view('config/list', $dados);
		
	}
	
	public function add(){	
	
			if($this->_post()) {
				
				$dataSave = array(				
										
							"nome" => $this->_post("nome"),					
							"codigo_paciente" => $this->_post("codigo_paciente"),					
							"data_nascimento" => $this->_post("data_nascimento"),					
							"email" => $this->_post("email"),					
							"telefone" => $this->_post("telefone"),					
							"celular" => $this->_post("celular"),
					'id_user' => $this->init->user['id_user'],
					'date_created' => date('Y-m-d H:i'),
					'date_update' => date('Y-m-d H:i')			
				);
				
				
				
				$this->pacientes_model->save($dataSave);
				
				$this->message->setMsg('success','Salvo com sucesso.');
					
			}
			
			$dados = array();
			
			
			
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


}

?>