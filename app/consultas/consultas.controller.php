<?php 

class consultas extends Controller {


	public function init(){
		
		$this->consultas_model = new consultasModel();
		
		
				
	}


	public function index_action(){
		
		$dados = array();
		
		$this->pagination = new Pagination();
		
		$count = $this->consultas_model->count();		
		$this->pagination->link('admin/consultas/index/page');			
		$this->pagination->setpaginate($count, ih_ItemsPerPage, ih_visibleItems, $this->_get('page') );
		
		$dados['list'] = $this->consultas_model->getAll($this->pagination->getLimit());
		
		$this->view('config/list', $dados);
		
	}
	
	public function add(){	
	
			if($this->_post()) {
				
				$dataSave = array(				
										
							"tipo_consulta" => $this->_post("tipo_consulta"),					
							"receituario" => $this->_post("receituario"),					
							"peso" => $this->_post("peso"),					
							"estatura" => $this->_post("estatura"),					
							"perimetro_cefalico" => $this->_post("perimetro_cefalico"),					
							"fraq_cardiaca" => $this->_post("fraq_cardiaca"),					
							"pressao_sis" => $this->_post("pressao_sis"),					
							"pressao_dia" => $this->_post("pressao_dia"),					
							"temperatura" => $this->_post("temperatura"),
					'id_user' => $this->init->user['id_user'],
					'date_created' => date('Y-m-d H:i'),
					'date_update' => date('Y-m-d H:i')			
				);
				
				
				
				$this->consultas_model->save($dataSave);
				
				$this->message->setMsg('success','Salvo com sucesso.');
					
			}
			
			$dados = array();
			
			
			
			$this->view('config/add', $dados);
	
	}
	
	public function details(){
		
		$id = $this->_get('id');
		
		$dados = array();
		
		
			
		$dados['data'] = $this->consultas_model->get($id);
			
		$this->view('config/details', $dados);
	
	}
	
	public function edit(){
		
		$id = $this->_get('id');
			
		if($this->_post()) {
				
			$dataSave = array(				
									
							"tipo_consulta" => $this->_post("tipo_consulta"),					
							"receituario" => $this->_post("receituario"),					
							"peso" => $this->_post("peso"),					
							"estatura" => $this->_post("estatura"),					
							"perimetro_cefalico" => $this->_post("perimetro_cefalico"),					
							"fraq_cardiaca" => $this->_post("fraq_cardiaca"),					
							"pressao_sis" => $this->_post("pressao_sis"),					
							"pressao_dia" => $this->_post("pressao_dia"),					
							"temperatura" => $this->_post("temperatura"),
				'id_user' => $this->init->user['id_user'],
				'date_update' => date('Y-m-d H:i')				
			);
			
			
				
			$this->consultas_model->edit($dataSave, 'id_consultas = '.$id);
				
			$this->message->setMsg('success','Editado com sucesso.');
					
		}
		
		$dados = array();
		
		
			
		$dados['data'] = $this->consultas_model->get($id);
		
		
			
		$this->view('config/edit', $dados);
	
	}
	
	public function del(){
		
		
		if($this->_post('id')){
			
			$id = $this->_post('id');
			
			$this->consultas_model->del('id_consultas = '.$id);
		}
	
	}


}

?>