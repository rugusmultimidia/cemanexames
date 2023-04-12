<?php 

class users extends Controller {

	public function init(){		

		//$this->add_js('templates/assets/js/jquery.rowsorter.min.js');	
		//$this->add_js('templates/assets/plugins/chosen/chosen.jquery.min.js');	
		$this->usersModel = new usersModel();
		$this->rolesModel = new rolesModel();		

	}

	public function index_action(){		

		$dados['list'] = $this->usersModel->getAll();
		$this->view('list', $dados);

	}

	public function add(){

		if($this->_post()) {		

				$save = array(
					'name' => $this->_post('name'),	
					'email' => $this->_post('email'),
					'login' => $this->_post('login'),
					'password' => md5($this->_post('pass')),
					'user_type' => $this->_post('user_type'),
					'data_created' => date('Y-m-d H:i'),
				);

				$this->usersModel->save($save);	
				$this->message->setMsg('success','Usuário criado com sucesso.');		

		}		

		$dados['roles'] = $this->rolesModel->getAll();		

		$this->view('add', $dados);		

	}	

	public function edit(){		

		$id = $this->_get('id');		

		if($this->_post()) {		

				$save = array(

					'name' => $this->_post('name'),
					'email' => $this->_post('email'),
					'login' => $this->_post('login'),
					
					'user_type' => $this->_post('user_type'),	
					'data_created' => date('Y-m-d H:i')	
				);	

				$password = $this->_post('pass');

				if(!empty($password)) {
					$save['password'] = md5($this->_post('pass'));
				}

				$this->usersModel->edit($save, 'id_user = '.$id);
				$this->message->setMsg('success','Usuário editado com sucesso.');		

		}		

		$dados['users'] = $this->usersModel->get($id);
		$dados['users'] = $dados['users'][0];
		$dados['roles'] = $this->rolesModel->getAll();		

		$this->view('edit', $dados);

	
	}

	public function del(){

		if($_POST['id']) {				

				$id = $_POST['id'];
				$this->usersModel->del('id_user = '.$id);			

		}

	}
	

	public function roles(){

		$dados['list'] = $this->rolesModel->getAll();
		$this->view('roles/list', $dados);
	}	

	public function roles_add(){	

		if($this->_post()) {		

				$save = array(
					'user_type' => $this->_post('name'),	
					'permissions' => serialize($_POST['permissao'])
				);				

				$this->rolesModel->save($save);				

				$this->message->setMsg('success','Papel criado com sucesso.');			

		}		

		$this->view('roles/add');	

	}

	

	public function roles_edit(){		

		$id = $this->_get('id');		

		if($this->_post()) {			

				$save = array(

					'user_type' => $this->_post('name'),
					'permissions' => serialize($_POST['permissao'])
				);			

				$this->rolesModel->edit($save, 'id_user_type = '.$id);
				$this->message->setMsg('success','Usuário editado com sucesso.');			

		}		

		$dados['role'] = $this->rolesModel->get($id);
		$dados['role'] = $dados['role'][0];	

		$dados['permission'] = unserialize($dados['role']['permissions']);	

		$this->view('roles/edit', $dados);

	}

	

	public function roles_del(){		

		if($_POST['id']) {			

				$id = $_POST['id'];			
				$this->rolesModel->del('id_user_type = '.$id);			

		}	

	}
	

	public function login() {	

		$this->add_js('app/users/assets/js/login.js');
		$this->add_css('app/users/assets/css/login.css');
		
			if($this->_post()) {	

				 $this->init->auth->setTableName('ih_users')
                         ->setUserColumn('login')
                         ->setPassColumn('password')
                         ->setUser($this->_post('user'))
                         ->setPass(md5($this->_post('pass')))
                         ->setLoginControllerAction('index', 'index')
                         ->login('@userApp');
			
				$this->message->setMsg('error','Usuário ou senha inválidos');
				$this->message->getMsg();				

				die();			

			}		

		$this->viewNoBase('login');		

	}

	public function logout() {		

		$this->init->auth->logout('@userApp', 'admin/users/login');

	}


}


?>