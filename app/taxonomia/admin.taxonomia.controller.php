<?php



	class Taxonomia extends Controller {
		
		
		
		public function init() {
			
			$this->title = 'taxonomia';	
			
			$this->add_js('app/taxonomia/assets/js/taxonomia.js');
			
			$this->vocabularios = new VocabulariosModel();
			$this->termos = new TermosModel();
			
		}
		
		
		
		public function index_action() {
			
			$dados = array();
			$dados['data'] = $this->vocabularios->getAll();
			
			$this->view('vocabulario/list', $dados);	

			
		}


		
		public function add() {
			
			
			if($_POST){
				
				$save = array(
					'taxonomy' => $_POST['nome']						
				);
				
				$this->vocabularios->save($save);
				
				$this->message->setMsg('success','Vocabulario salvo com sucesso.');
					
			}
			
			$this->view('vocabulario/add');			
		}
		
		public function edit() {
			
			$id = $this->_get('id');
			
			if($_POST and $id){

					
					$save = array(
						'taxonomy' => $_POST['nome'],					
					);
					
					$this->vocabularios->edit($save,'id_taxonomy = '.$id);
					
					$this->message->setMsg('success','Vocabulario salvo com sucesso.');

			
			}
			
			$dados['vocabulario'] = $this->vocabularios->get($id);
			
			$this->view('vocabulario/edit', $dados);			
		}
		
		
		
		public function del(){
			
			if($_POST['id']) {
				
				$id = $_POST['id'];
			
				$this->vocabularios->del('id_taxonomy = '.$id);
			
			}
			
		}
		
		public function termos() {
			
			
			if($this->_post('id')){

				$save = array(
					'taxonomy_entity' => $this->_post('nome')		
				);
				
				$this->termos->edit($save, 'id_taxonomy_entity = '.$this->_post('id'));
				
				$this->message->setMsg('success','termo salvo com sucesso.');
					
			} else if ($this->_post()){
				
				$save = array(
					'id_entity' => $this->_post('vocabulario'),
					'taxonomy_entity' => $this->_post('nome')				
				);
				
				$this->termos->save($save);
				
				$this->message->setMsg('success','termo salvo com sucesso.');
					
			}
			
			$dados['id_vocabulario'] = $this->_get('vocabulario');
			
			$dados['vocabulario'] = $this->vocabularios->get($dados['id_vocabulario']);
			
			$dados['termos'] = $this->termos->getAll($dados['id_vocabulario']);			
			
			$this->view('termos/list', $dados);	
			
						
		}

		
		public function add_termo(){					
			
			$this->viewNoBase('termos/add');
		}
		
		public function edit_termo(){
				
			$dados['termo'] = $this->termos->get($this->_get('vocabulario'));
			
			$this->viewNoBase('termos/edit', $dados);
		}
		
		public function del_termo(){
			
			if($_POST['id']) {
				
				$id = $_POST['id'];
			
				$this->termos->del('id_taxonomy_entity = '.$id);
			
			}
			
		}
		
		
	}


?>