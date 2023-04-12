<?php

/*
--------- Init Class ------------
Utilizado para criar metodos globais para uma rota.
São automaticamente chamados caso existam:
metodo init_{route name}

*/


class init {	

		public $auth, $xcodeModel;		

		public function __construct(){			

			$this->auth 	  = new Auth();
			$this->xcodeModel = new xcodeModel();

		}		

		public function init(){

			//default init if dont have a route.
			$this->auth->checkLogin('@userAppPaciente', 'redirect',ih_base_path.'users/login', true);
			$this->user = $this->auth->userData('@userAppPaciente');		
			$this->session = new Session();	

		}
		

		public function init_admin(){			

			if(isset($_GET['debug'])) {
				return true;
			}

			$this->auth->checkLogin('@userApp', 'redirect',ih_base_path.'admin/users/login', true);
			$this->user = $this->auth->userData('@userApp');			

		}
	

}