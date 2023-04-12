<?php 

class {classname} extends Controller {


	public function init(){
		
		$this->{classname}_model = new {classname}Model();
		
		{helpers}
				
	}


	public function index_action(){
		
		$dados = array();
		
		$this->pagination = new Pagination();
		
		$count = $this->{classname}_model->count();		
		$this->pagination->link('admin/{classname}/index/page');			
		$this->pagination->setpaginate($count, ih_ItemsPerPage, ih_visibleItems, $this->_get('page') );
		
		$dados['list'] = $this->{classname}_model->getAll($this->pagination->getLimit());
		
		$this->view('config/list', $dados);
		
	}
	
	public function add(){	
	
			if($this->_post()) {
				
				$dataSave = array(				
					{fields}
					'id_user' => $this->init->user['id_user'],
					'date_created' => date('Y-m-d H:i'),
					'date_update' => date('Y-m-d H:i')			
				);
				
				{setImage}
				
				$this->{classname}_model->save($dataSave);
				
				$this->message->setMsg('success','Salvo com sucesso.');
					
			}
			
			$dados = array();
			
			{entitys}
			
			$this->view('config/add', $dados);
	
	}
	
	public function details(){
		
		$id = $this->_get('id');
		
		$dados = array();
		
		{entitys}
			
		$dados['data'] = $this->{classname}_model->get($id);
			
		$this->view('config/details', $dados);
	
	}
	
	public function edit(){
		
		$id = $this->_get('id');
			
		if($this->_post()) {
				
			$dataSave = array(				
				{fields}
				'id_user' => $this->init->user['id_user'],
				'date_update' => date('Y-m-d H:i')				
			);
			
			{setImage}
				
			$this->{classname}_model->edit($dataSave, '{id} = '.$id);
				
			$this->message->setMsg('success','Editado com sucesso.');
					
		}
		
		$dados = array();
		
		{entitys}
			
		$dados['data'] = $this->{classname}_model->get($id);
		
		{multiples_fields}
			
		$this->view('config/edit', $dados);
	
	}
	
	public function del(){
		
		
		if($this->_post('id')){
			
			$id = $this->_post('id');
			
			$this->{classname}_model->del('{id} = '.$id);
		}
	
	}


}

?>