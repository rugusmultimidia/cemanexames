<?php 



class users extends Controller {


	public function init(){

		$this->usersModel = new usersModel();
		$this->rolesModel = new rolesModel();

	}

	public function index_action(){		


	}

	

	public function login() {	

		$this->add_js('app/users/assets/js/login.js');

		$this->add_css('app/users/assets/css/login.css');			

			if($this->_post()) {

				 $this->init->auth->setTableName('tb_pacientes')
                         ->setUserColumn('codigo_paciente')
                         ->setPassColumn('senha')
                         ->setUser($this->_post('user'))
                         ->setPass($this->_post('pass'))
                         ->setLoginControllerAction('index', 'index')
                         ->login('@userAppPaciente');			

				$this->message->setMsg('error','Código ou senha inválidos');

				$this->message->getMsg();			

				die();		

			}		

		$this->viewNoBase('login_paciente');	

	}	

	public function logout() {		

		$this->init->auth->logout('@userAppPaciente', 'users/login');

	}

}


?>