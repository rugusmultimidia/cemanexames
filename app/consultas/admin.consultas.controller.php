<?php 

class consultas extends Controller {


	public function init(){
		
		$this->consultas_model = new consultasModel();
		$this->pacientes_model = new pacientesModel();
		
				
	}


	public function lista(){


		$dados = array();

		$pacienteID = $this->_get('paciente');

		$dados['paciente'] = $this->pacientes_model->get($pacienteID);
		$dados['paciente'] = $dados['paciente'][0];

		//print_r($dados['paciente']); die;
		
		
		
		$this->pagination = new Pagination();
		
		$count = count($this->consultas_model->getAll($pacienteID));		
		$this->pagination->link('admin/consultas/index/page');			
		$this->pagination->setpaginate($count, ih_ItemsPerPage, ih_visibleItems, $this->_get('page') );
		
		$dados['list'] = $this->consultas_model->getAll($pacienteID, $this->pagination->getLimit());
		
		$this->view('config/list', $dados);
		
	}
	
	public function add(){	
	
			if($this->_post()) {

				$data = implode('-', array_reverse(explode('/', $this->_post("data"))));
				
				$dataSave = array(				
										
					"id_paciente" => $this->_get("paciente"),					
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
					'date_created' => $data,
					'date_update' => date('Y-m-d H:i')			
				);
				
				
				
				$this->consultas_model->save($dataSave);
				
				$this->message->setMsg('success','Salvo com sucesso.');

				//header('location:')
					
			}
			
			$dados = array();
			
			
			
			$this->view('config/add', $dados);
	
	}

	
	public function edit(){
		
		$id = $this->_get('id');
			
		if($this->_post()) {

			$data = implode('-', array_reverse(explode('/', $this->_post("data"))));

				
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
				'date_created' => $data,
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

	public function getTipoConsulta($number) {
		
		if($number == 2) return "Rotina"; 
		if($number == 1) return "Primeira"; 
		if($number == 3) return "Avaliação"; 
		if($number == 4) return "Retorno"; 
	}
}

?>