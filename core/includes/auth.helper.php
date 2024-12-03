 <?php

class Auth {

    protected $sessionHelper, $redirectorHelper, $tableName, $userColumn, $passColumn, 

              $user, $pass, $loginController = 'index', $loginAction = 'index',

              $logoutController = 'index', $logoutAction = 'index';
    

    public function __construct() {
       

        $this->sessionHelper = new Session();
        $this->redirectorHelper = new Redirector();

        return $this;
    }

    

    public function setTableName($value) {        

        $this->tableName = $value;        

        return $this;      

    }


    public function setUserColumn($value) {       

        $this->userColumn = $value;       

        return $this;       

    }

    

    public function setPassColumn($value) {       

        $this->passColumn = $value;       

        return $this;       

    }

    public function setUser($value) {        

        $this->user = $value;        

        return $this;        

    }

    public function setPass($value) {        

        $this->pass = $value;       

        return $this;        

    } 

    public function setLoginControllerAction($controller, $action) {        

        $this->loginController = $controller;
        $this->loginAction = $action;        

        return $this;      

    }    

    public function setLogoutControllerAction($controller, $action) {        

        $this->logoutController = $controller;

        $this->logoutAction = $action;        

        return $this;        

    }     

    public function login($user, $action = false) { 

        $db = new Model();		

        $db->table = $this->tableName;        

        $where = "u.".$this->userColumn . "='" . $this->user . "' and u." . $this->passColumn . "='" . $this->pass . "'";	

        if(!empty($this->tableName)) {

            $q = 'SELECT u.*, p.user_type as name_role, p.permissions
                    FROM ih_users u
                    LEFT JOIN ih_users_type p on p.id_user_type = u.user_type
                    WHERE '.$where;

            // die($q);

            $sql = $db->executeSql( $q );

            if(count($sql) > 0) {
                $permissions = unserialize($sql[0]['permissions']);
                $sql[0]['permissions'] = $permissions;
            }


        } else {	

            $q = '
    				SELECT * FROM ih_users
    	            LEFT JOIN ih_users_type
    				ON ih_users.user_type = ih_users_type.id_user_type
    				WHERE 
    				ih_users.'.$this->userColumn.' = "'.$this->user.'"
    				and
    				ih_users.'.$this->passColumn.' = "'.$this->pass.'"
    		';

            die($q);

            $sql = $db->executeSql( $q );

           

        }

        if (count($sql) > 0){            

            $this->sessionHelper->createSession($user, $sql[0]);

            if($action) {

            	$this->redirectorHelper->goToAction(ih_current_path.$action);

			} else {

				die(true);	

			}            

        }      

        return $this;       

    }

    

    public function logout($user, $action) {        

        $this->sessionHelper->deleteSession("userAuth", true);

        $this->sessionHelper->deleteSession($user, $sql[0]);        

        $this->redirectorHelper->goToAction(PATH.$action);        

        return $this;       

    }    

    

    public function checkLogin($user, $type, $action, $ignorePermission = false) { 

        // die($action);
        // $userData = $this->userData($user);
        // echo "<pre>";
        // print_r($userData['user_type']);
        // var_dump($userData['user_type'] == 1 || $userData['user_type'] == 2);
        // print_r($_SESSION);
        // die;

        $userType = $_SESSION['@userApp']['user_type'];

        if ($userType == 1 || $userType == 2) {
            // die("admin");
            return false;
        }

        if ($this->redirectorHelper->getCurrentController() == "users" 
            && ($this->redirectorHelper->getCurrentAction() == "login" || $this->redirectorHelper->getCurrentAction() == "logout")) {
            return false;
        }


        if ($this->sessionHelper->checkSession($user)) {
            $userData = $this->userData($user);
            if ($userData['user_type'] == 1 || $userData['user_type'] == 2) {
                return true;
            }
        }

    $headers = apache_request_headers();
    
    if (isset($headers['Authorization'])) {
        $token = $headers['Authorization'];
        // Validate  tokenthe (this is just an example, implement your own validation logic)
        if ($token === 'G423JHG46GJH546F7F3763UI356KJ356') {

            // Get the JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            // die(var_dump($input));

            // die(json_encode(var_dump($input)));

            if (isset($input['login']) && isset($input['senha'])) {
                $login = $input['login'];
                $senha = $input['senha'];
                $clinica = $input['clinica'];

                $cpf = mb_convert_encoding($input['login'], 'UTF-8', 'auto');
                $senha = mb_convert_encoding($input['senha'], 'UTF-8', 'auto');
                $clinica = mb_convert_encoding($input['clinica'], 'UTF-8', 'auto');

                $db = new Model();	

                $q = "
                SELECT u.*
                FROM tb_pacientes u
                WHERE 
                    (u.cpf = '$login' OR u.codigo_paciente = '$login')
                    AND (u.data_nascimento = '$senha' OR u.senha = '$senha')
                    AND u.clinica = '$clinica'
                LIMIT 1
                ";

                // die(json_encode($q));

                $sql = $db->executeSql($q);

                die(json_encode($sql));
                
                if (count($sql) > 0) {
                    
                    die(json_encode($sql[0]));
                    $this->sessionHelper->createSession("@paciente", $sql[0]);
                    return true;

                } else {
                    header('Content-Type: application/json');
                    http_response_code(200);
                    die(json_encode(array('status' => 'error', 'message' => 'Usuário ou senha inválidos.')));

                }
            } else {
                $_SESSION['msg_erro'] = 'Dados de login não encontrados.';
                $this->redirectorHelper->goToAction('/login');
                die;
            }
        } else {
            $_SESSION['msg_erro'] = 'Token inválido.';
            $this->redirectorHelper->goToAction('/login');
            die;
        }
    } 


        if (!$action) {                 
            if ($this->sessionHelper->checkSession($user)) {                
                return true;                
            } else {                    
                return false;                
            }                
        } else {     
            if (!$this->sessionHelper->checkSession($user)) {
                // User not logged in
                $this->redirectorHelper->goToAction($action);                         
            } else {                    
                // User logged in, check permissions
                $userData = $this->userData($user);
                $permissions = $userData['permissions'];   
                // echo "<pre>";die(var_dump($permissions)); 
                
                // User type 0 is Developer user, don't need to check permission.
                if ($userData['user_type'] == 1) {
                    return;
                }
                // echo "<pre>";die(var_dump($this->redirectorHelper->getCurrentController()));     
                
                // Check if user has access to the current controller
                if (isset($permissions[$this->redirectorHelper->getCurrentController()])) {   
                    
                    // echo "<pre>";die(print_r($permissions[$this->redirectorHelper->getCurrentController()]));                                        
                    // echo "<pre>";die(var_dump($this->redirectorHelper->getCurrentAction())); 
                    // echo "<pre>";die(var_dump($dataAction)); 
                    // echo "<pre>";die(var_dump(in_array($this->redirectorHelper->getCurrentAction(), $permissions[$this->redirectorHelper->getCurrentController()]))); 

                    if (in_array($this->redirectorHelper->getCurrentAction(), $permissions[$this->redirectorHelper->getCurrentController()])) {
                        return;
                    } else {
                        $this->redirectorHelper->goToAction('/admin/index');
                        $_SESSION['msg_erro'] = 'Você não tem permissão para acessar essa página.';                            
                        die;
                    }
                } else {
                    if ($this->redirectorHelper->getCurrentController() == 'index') {
                        return;
                    } else {
                        $this->redirectorHelper->goToAction('index');
                        $_SESSION['msg_erro'] = 'Você não tem permissão para acessar essa página.';
                        // $this->redirectorHelper->goToAction($action);
                    }                            
                }
                die;
            }
        }        
    } 

    

	//Return the user data information in session

    public function userData($user) {

        

        return $this->sessionHelper->selectSession($user);

        

    }

    

}



?>

