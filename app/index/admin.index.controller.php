<?php 



class index extends Controller {

	public function init(){		

		$this->indexModel = new indexModel();

	}

	public function index_action(){

		$this->view('home');
	}

	public function login() {

		if ($_POST){    				 

	        $this->auth->setTableName('users')
                 ->setUserColumn('user')
                 ->setPassColumn('pass')
                 ->setUser($_POST['user'])
                 ->setPass($_POST['pass'])
                 ->setLoginControllerAction('index', 'index')
                 ->login('@userApp');

            $this->msg->setMsg('error', 'usuario ou senha invalidos.'); 
			$this->msg->getMsg();
			die('');              

		}		

		$this->viewNoBase('login');
	}

	public function forbidden() {
		$this->viewNoBase('acesso_negado');
	}

	public function logout() {

			$this->auth->logout('@userApp','');

	}

	public function cartao(){

		$this->pacientesModel = new pacientesModel();

		$dados = array();

		$id = $this->_get('id');

		$dados['data'] = $this->pacientesModel->get($id);	 

		// $this->printar($dados);

		$this->viewNoBase('cartao', $dados);

	}


	public function interacao_medicamentosa(){


		$this->medicamentos = $this->indexModel->getallMedicamentos();

		$this->view('interacao_medicamentosa');

	}


	public function get_interacoes(){
		
		$this->interacoes = $this->indexModel->getInteracoes($_POST['medicamento']);

		print json_encode($this->interacoes);

		exit();

	}


	

}

?>