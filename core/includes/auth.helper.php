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

        $where = $this->userColumn . "='" . $this->user . "' and " . $this->passColumn . "='" . $this->pass . "'";	

        if(!empty($this->tableName)) {
 
            $sql = $db->executeSql('
                    SELECT * FROM '.$this->tableName.' where '.$where .'
            ');

        } else {	

            $sql = $db->executeSql('
    				SELECT * FROM ih_users
    				INNER JOIN ih_users_type
    				ON ih_users.user_type = ih_users_type.id_user_type
    				WHERE 
    				ih_users.'.$this->userColumn.' = "'.$this->user.'"
    				and
    				ih_users.'.$this->passColumn.' = "'.$this->pass.'"
    		');

           

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

    

    public function checkLogin( $user, $type, $action, $ignorePermisson = false ) { 

		if  ($this->redirectorHelper->getCurrentController() == "users" 

		and ($this->redirectorHelper->getCurrentAction() == "login" || $this->redirectorHelper->getCurrentAction() == "logout" ) ) {

			return false;

		}

        if ($this->sessionHelper->checkSession($user) and $ignorePermisson == true) {
            return;
        } 	

		$dataAction = array('edit', 'del', 'details', 'index_action');		

        if (!$action) {                 

                if ($this->sessionHelper->checkSession($user)) {				

                    return true;                

                } else {                    

                    return false;                

				}				

        } else {          		

                if (!$this->sessionHelper->checkSession($user)) {

					//user not logged		   

                    $this->redirectorHelper->goToAction($action);                         

                } else {					

					//user logged, but we need check permissions


					$userData = $this->userData($user);


					$permission = unserialize($userData['permissions']);					

					//user type 0 is Developer user, don't need to check permission.

					if($userData['user_type'] == 0 || $userData['user_type'] == 1){

						return;

					}

					//check if have access in controller

					if(isset($permission[$this->redirectorHelper->getCurrentController()])){						

						

						if(in_array($this->redirectorHelper->getCurrentAction(), $dataAction)) {

							if($this->redirectorHelper->getCurrentAction())

							//if has access in controller, check if has access in action

							if(isset($permission[$this->redirectorHelper->getCurrentController()][$this->redirectorHelper->getCurrentAction()]) 

								and  $permission[$this->redirectorHelper->getCurrentController()][$this->redirectorHelper->getCurrentAction()]){

							

								return;

								

							} else {							

								$this->redirectorHelper->goToAction($action);							

							}

							

						} else {

							return;

						}

						

					} else {



						if($this->redirectorHelper->getCurrentController() == 'index') {

							return;

						} else {						

							$this->redirectorHelper->goToAction($action);

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

