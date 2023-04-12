<?php

###################################################
#												  #
# ---------    Wind Framework         ----------  #
# ---------    Classe autoload        ----------  #
# ---------    Versão 3.0 		      ----------  #
# ---------    Caio Cesar - 2012      ----------  #
# ---------    caiocmm@live.com       ----------  #
#												  #
###################################################


class autoloader {

    public static $loader;

    public static function init()
    {
        if (self::$loader == NULL)
            self::$loader = new self();

        return self::$loader;
    }

    public function __construct()
    {
		
	
        spl_autoload_register(array($this,'model'));
        spl_autoload_register(array($this,'helper'));
        spl_autoload_register(array($this,'controller'));
        spl_autoload_register(array($this,'core'));
		
		$this->core = new Core();
    }

    public function core($class)
    {	
		$path = IH_ROOT.'/core/';
		
		if (file_exists($path.strtolower($class).'.php')) {	
			require_once $path.strtolower($class).'.php';
		}
    }

    public function controller($class)
    {	
		$path = strtolower(IH_ROOT.'/app/'.$class);
		
		if (file_exists($path.$class.'.controller.php')) {
			
			if ($path) {  	

				require_once $path.$class.'.controller.php';
			
			}
		}
    }

    public function model($class)
    {
        if ($class) {  
			
			if(isset($this->core->controller)){
			
				$path = strtolower(IH_ROOT.'/app/'.$this->core->controller.'/models/');

				if (file_exists($path.$class.'.php')) {  	
				     
					require_once $path.$class.'.php';
					
				} else {
					
					$name_class = explode('Model',$class);
					
					$path = strtolower(IH_ROOT.'/app/'.$name_class[0].'/models/');

					if (file_exists($path.$class.'.php')) {  	   
						
						require_once $path.$class.'.php';
					
					}
					
				}
			}
		} 
    }

    public function helper($class){

        if ($class) {
			
			$path = strtolower(IH_ROOT.'/core/includes/');
			
			if(file_exists($path.strtolower($class).'.helper.php')) { 

				require_once $path.strtolower($class).'.helper.php';

			} 
			
        } 
		
			
		
    }

}

?>