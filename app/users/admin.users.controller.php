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
		$dados['roles'] = $this->rolesModel->getAll();
		$this->view('list', $dados);

	}

	public function add(){

		if($this->_post()) {		

				$save = array(
					'name' => $this->_post('name'),	
					'email' => $this->_post('email'),
					'login' => $this->_post('login'),
					'clinica' => $this->_post('clinica'),
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
					'clinica' => $this->_post('clinica'),
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

		if ($this->getUserType() != 1) {
			header('Location: /');
			exit();
		}

		// $this->printar($_SESSION);

		$dados['list'] = $this->rolesModel->getAll();
		$this->view('roles/list', $dados);
	}	

	public function roles_add(){	

		if ($this->getUserType() != 1) {
			header('Location: /');
			exit();
		}

		if($this->_post()) {		

				$save = array(
					'funcao' => 0,	
					'user_type' => $this->_post('name'),	
					'permissions' => serialize($_POST['permissions'])
				);				

				$this->rolesModel->save($save);				

				$this->message->setMsg('success','Papel criado com sucesso.');	
				
				header('Location: /admin/users/roles');
				exit();

		}	
		
		$controllers = glob('app/*/*controller.php');
		$controllers = array_filter($controllers, function($controller) {
			return preg_match('/^app\/.*\/admin\..*controller\.php$/', $controller);
		});

		$dados['modulos'] = $this->updatePermissionsArray($controllers);

		$this->view('roles/add', $dados);	

	}

	

	public function rolesEdit(){	
		
		if ($this->getUserType() != 1) {
			header('Location: /');
			exit();
		}

		$id = $this->_get('id');		

		if($this->_post()) {	
			
			// $this->printar($_POST);

				$save = array(

					'user_type' => $this->_post('name'),
					'permissions' => serialize($_POST['permissions'])
				);			

				$this->rolesModel->edit($save, 'id_user_type = '.$id);
				$this->message->setMsg('success','Usuário editado com sucesso.');			

		}		

		$dados['role'] = $this->rolesModel->get($id);
		$dados['role'] = $dados['role'][0];	

		$dados['permission'] = unserialize($dados['role']['permissions']);	

		// $this->printar($dados['permission']);

		$controllers = glob('app/*/*controller.php');
		$controllers = array_filter($controllers, function($controller) {
			return preg_match('/^app\/.*\/admin\..*controller\.php$/', $controller);
		});

		$dados['modulos'] = $this->updatePermissionsArray($controllers);

		// $this->printar($dados['modulos']);

		$this->view('roles/edit', $dados);

	}

	private function updatePermissionsArray($caminhosControllers) {

		$classes = [];

		foreach ($caminhosControllers as $controller) {
			preg_match('/^app\/(.*?)\/admin\..*controller\.php$/', $controller, $matches);
			if (isset($matches[1])) {
				$classes[] = ['path' => $controller,
						'class' => $matches[1]
					];
			}
		}

		// $this->printar($classes);

		$class = [];
		
		foreach ($classes as &$class) {
			$permission[]=[
				"modulo" => $class['class'],
				"funcoes" => $this->getPublicMethodsFromController($class['path'], $class['class'])
			];
		}

		// $this->printar($class);
		return $permission;
    
	}
	
	private function getPublicMethodsFromController($filePath, $className) {
        require_once $filePath;
        $publicMethods = [];

		// echo $className.'<br>';
		// echo $filePath.'<br>';

        if (class_exists($className)) {
            $reflectionClass = new ReflectionClass($className);
            foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->class == $className && !$method->isConstructor()) {
                    $publicMethods[] = $method->name;
                }
            }
        }

		// echo '<pre>';
		// print_r($publicMethods);	

        return $publicMethods;
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