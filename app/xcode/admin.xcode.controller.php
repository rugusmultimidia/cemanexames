<?php 

class xcode extends Controller {


	public function init(){
		
		$this->add_js('themes/admin/assets/js/jquery.rowsorter.min.js');	
		$this->add_js('themes/admin/assets/plugins/chosen/chosen.jquery.min.js');		
		$this->add_js('app/xcode/assets/js/xcode.js');

		$this->add_js('app/xcode/assets/js/views.js');
		
		$this->xcodeModel = new xcodeModel();
		$this->viewModel = new viewModel();
	}


	public function index_action(){
		
		
		$dados['list'] = $this->xcodeModel->getAll();
		
		$this->view('modules/list', $dados);
		
	}
	
	
	
	public function add(){

		if($this->_post()) {
			
			$this->generate($this->_post(), true);
			
		}
		
		$this->view('modules/add');
		
	}
	
	public function edit(){
		
		if($this->_post()) {
			
			$fields__ = $_POST['field'];
			
			/*
			for ($x = 0; $x <= 13; $x++) {
				
				foreach($fields__ as $key => $value) {
					$_POST['field'][$key.$x] = $value;
					$_POST['field'][$key.$x]['field_machine_name'] = $value['field_machine_name'].'_'.$x;
				}

			} 
			*/
			
			//print_r($_POST);
			//die;
			$this->generate($this->_post(), false);
			
		}
		
		$dados['data'] =  $this->xcodeModel->get($this->_get('id'));

		$dados['data_fields'] = base64_decode($dados['data'][0]['data']);
		$dados['data_fields'] = unserialize($dados['data_fields']);
		
		//print_r($dados['data_fields']);die;
		
		
		$this->view('modules/edit', $dados);
	
	}
	
	
	/*-------------- Views --------------*/
	/*
		Views is used to list a content 		
	*/
	
	public function views(){
	
		
	
	}
	
	
	public function views_add(){	
	
		if($this->_post()) {
			
			$this->generate($this->_post(), true);
			
		}
		
		$dados['modules'] = $this->xcodeModel->getAll();
		
		
		$this->view('view_module/add', $dados);
	
	}
	
	public function views_edit(){
	
	
		
	
	}
	
	
	private function generate($data, $isNew){		
		
//print_r($data); die;
		$controller = $data['controller'];
		$data['controller'] = strtolower(functions::removeSpecialChars($data['controller']));
		
		if(!$isNew){
			$this->del_tree('app/'.$data['controller']);
		}
		
		$path = 'app/'.$data['controller'].'/';
		
		$i= 0;
		foreach($_POST['field'] as $key => $val){
				$_POST['field'][$i] = $val;
				//$_POST['field'][$i]['field_machine_name'] = substr($_POST['field'][$i]['field_machine_name'],0,30);				
				unset($_POST['field'][$key]);
				$i++;
		}
		
		
		if(!file_exists($path)) {
			
			$controller_data = array(
				'controller' => $controller,
				'data'		 => base64_encode(serialize($_POST))
			);
				
		
			
		if($isNew){
			//save data array into datatable
			$this->xcodeModel->save($controller_data);
		} else {
			$this->xcodeModel->edit($controller_data, 'id_controller = '.$data['id_controller']);
		}
		
			mkdir($path);

			mkdir($path .'models');
			mkdir($path .'views');
			mkdir($path .'views/config');
			
			$__fields = '';
			
			//print_r($_POST);DIE;
			
			/*------ controller File Admin --------*/
			
				//copy the sample controller to module.
				copy("app/xcode/includes/sample.controller.php", "app/".$data['controller']."/admin.".$data['controller'].".controller.php");
			
				//get the admin file
				$file_controller_admin = @file_get_contents("app/".$data['controller']."/admin.".$data['controller'].".controller.php");
				
				//generate estructure to save / edit
				
				$helpers = ''; $image = ''; $entitys = ''; $multiples_fields = '';
						
				
				foreach($_POST['field'] as $_field) {
					
					if(!isset($_field['deleted'])){
					
						if($_field['field_type'] == 'image') {
							
							$helpers .= '$this->uploder = new Upload();';					
							
							$image .= 'if($_FILES["'.$_field['field_machine_name'].'"]["error"] == 0) {					
											$this->uploder->setFile($_FILES["'.$_field['field_machine_name'].'"]);									
											$dataSave["'.$_field['field_machine_name'].'"] = $this->uploder->upload();}';
							
						} elseif ($_field['field_type'] == 'entity'){
							
							$model = explode('@',$_field['options']['entity']);
							
							$helpers .= '$this->'.$model[0].'_model = new '.$model[0].'Model();';	
							
							$entitys .= '$dados["'.$model[0].'"] = $this->'.$model[0].'_model->getAll();';
							
							if($_field['field_multiple_values'] == "true") {
								$post_field = 'serialize($this->_post("'.$_field['field_machine_name'].'")),';
								$multiples_fields .='$dados["'.$_field['field_machine_name'].'"] = unserialize($dados["data"][0]["'.$_field['field_machine_name'].'"]);';
							} else {
								$post_field = '$this->_post("'.$_field['field_machine_name'].'"),';
							}
							
							$__fields .= '					
							"'.$_field['field_machine_name'].'" => '.$post_field ;
						
						} elseif($_field['field_type'] == 'markup') {
							
							
						} else {
							
							$post_field = '$this->_post("'.$_field['field_machine_name'].'"),';
							
							$__fields .= '					
							"'.$_field['field_machine_name'].'" => '.$post_field ;
						}
					
					}
							
					
				}
				
				//replace values
				$file_controller_admin = str_replace('{classname}', $data['controller'], $file_controller_admin);
				$file_controller_admin = str_replace('{helpers}', $helpers, $file_controller_admin);
				$file_controller_admin = str_replace('{setImage}', $image, $file_controller_admin);
				$file_controller_admin = str_replace('{fields}', $__fields, $file_controller_admin);
				$file_controller_admin = str_replace('{entitys}', $entitys, $file_controller_admin);
				$file_controller_admin = str_replace('{multiples_fields}', $multiples_fields, $file_controller_admin);
				$file_controller_admin = str_replace('{id}', 'id_'.$data['controller'], $file_controller_admin);
				
				
				//save file
				file_put_contents("app/".$data['controller']."/admin.".$data['controller'].".controller.php", $file_controller_admin);
				
			/*------ controller File Default --------*/
			
				//copy the sample controller to module.
				copy("app/".$data['controller']."/admin.".$data['controller'].".controller.php", "app/".$data['controller']."/".$data['controller'].".controller.php");
			
				//get the file
				//$file_controller = @file_get_contents("app/".$data['controller']."/admin.".$data['controller'].".controller.php");
				
				//replace values
				//$file_controller = str_replace('{classname}', $data['controller'], $file_controller);			
				
				//save file
				//file_put_contents("app/".$data['controller']."/admin.".$data['controller'].".controller.php", $file_controller);
				
			/*------ Model File --------*/
			
				//copy the sample model to module.
				copy("app/xcode/includes/sampleModel.php", "app/".$data['controller']."/models/".$data['controller']."Model.php");
			
				//get the file
				$file_model = @file_get_contents("app/".$data['controller']."/models/".$data['controller']."Model.php");
				
				//replace values
				$file_model = str_replace('{classname}', $data['controller'].'Model', $file_model);
				$file_model = str_replace('{id}', 'id_'.$data['controller'], $file_model);
				$file_model = str_replace('{table}', $data['controller'], $file_model);					
				
				//save file
				file_put_contents("app/".$data['controller']."/models/".$data['controller']."Model.php", $file_model);
			
			
			/*------ views Files --------*/
			
			copy("app/xcode/includes/add.phtml", "app/".$data['controller']."/views/config/add.phtml");
			copy("app/xcode/includes/edit.phtml", "app/".$data['controller']."/views/config/edit.phtml");
			copy("app/xcode/includes/list.phtml", "app/".$data['controller']."/views/config/list.phtml");
			copy("app/xcode/includes/details.phtml", "app/".$data['controller']."/views/config/details.phtml");

			//get the files
			$file_add  = @file_get_contents("app/".$data['controller']."/views/config/add.phtml");
			$file_edit = @file_get_contents("app/".$data['controller']."/views/config/edit.phtml");
			$file_list = @file_get_contents("app/".$data['controller']."/views/config/list.phtml");
			$file_detail = @file_get_contents("app/".$data['controller']."/views/config/details.phtml");
			
			
			
			//---------- Add ------------//
			
			$file_add = str_replace('{controller}', $controller, $file_add);
			
			$fields_add = '';	
			
			foreach($_POST['field'] as $_field) {
				
				if(!isset($_field['deleted'])){
				
					if(!isset($_field['options']))$_field['options'] = '';
					
					
						if($_field['field_type'] == 'markup') {
							
							$fields_add .= $this->get_field_type($_field, false);							
						} else {
						
							$fields_add .= 
							'<div class="col-xs-'.$_field['grid'].' name-'.$_field['field_machine_name'].' type-'.$_field['field_type'].'">
								<div class="form-group fields">
									<label for="'.$_field['field_machine_name'].'">'.$_field['field_name'].'</label>
									'.$this->get_field_type($_field, false).'
								</div>
							</div>'.PHP_EOL;
						
						}
					
				}
					
			}
			
			$file_add = str_replace('{fields_add}', $fields_add, $file_add);	
			
			//---------- Edit ------------//
			
			$file_edit = str_replace('{controller}', $controller, $file_edit);
			
			$fields_edit = '';			
			
			foreach($_POST['field'] as $_field) {
				
				if(!isset($_field['deleted'])){
				
					if(!isset($_field['options']))$_field['options'] = '';
					
						if($_field['field_type'] == 'markup') {
							
							$fields_edit .= $this->get_field_type($_field, false);							
						} else {
								
							$fields_edit .= 
							'<div class="col-xs-'.$_field['grid'].' name-'.$_field['field_machine_name'].' type-'.$_field['field_type'].'">
								<div class="form-group fields">
									<label for="'.$_field['field_machine_name'].'">'.$_field['field_name'].'</label>
									'.$this->get_field_type($_field, true).'
								</div>
							</div>'.PHP_EOL;
						}
				}
					
			}
			
			$file_edit = str_replace('{fields_edit}', $fields_edit, $file_edit);
			
			
			//---------- details ------------//
			
			$file_detail = str_replace('{controller}', $controller, $file_detail);
			
			$fields_detail = '';			
			
			foreach($_POST['field'] as $_field) {
				
				if(!isset($_field['deleted'])){				
				
				if(!isset($_field['options']))$_field['options'] = '';
					
					$fields_detail .= 
					'<div class="col-xs-'.$_field['grid'].'">
						<div class="form-group fields">
							<label for="'.$_field['field_machine_name'].'">'.$_field['field_name'].'</label>
							<?php print $view_data[0]["'.$_field['field_machine_name'].'"];  ?>
						</div>
					 </div>'.PHP_EOL;		
					 
				}
					
			}
			
			$file_detail = str_replace('{fields_details}', $fields_detail, $file_detail);
			
			
			//---------- View List ------------//

			$labels_view = '';
			$fields_view = '';
			
			foreach($_POST['field'] as $__field) { 
				
				if(isset($__field['colum_list'])){
					$labels_view .= '<td>'.$__field['field_name'].'</td>'.PHP_EOL; 
					
					if($__field['field_type'] == 'image'){ 
						$fields_view .= '<td><img src="themes/files/uploads/<?php print $dados["'.$__field['field_machine_name'].'"]; ?>" width="90" height="70" /></td>'.PHP_EOL;
					} elseif($__field['field_type'] == 'entity') {
						
						$model = explode('@',$__field['options']['entity']);
						
						$fields_view .= '<td><?php print $this->'.$model[0].'_model->getValue($dados["'.$__field['field_machine_name'].'"], "'.$model[1].'"); ?></td>'.PHP_EOL; 
					} else {
						$fields_view .= '<td><?php print $dados["'.$__field['field_machine_name'].'"]; ?></td>'.PHP_EOL; 
					}
				}
			}
	
			
			$file_list = str_replace('{controller}', $controller, $file_list);
			$file_list = str_replace('{id}', 'id_'.$data['controller'], $file_list);
			$file_list = str_replace('{labels_view}', $labels_view, $file_list);
			$file_list = str_replace('{fields_view}', $fields_view, $file_list);			
			
			
			
			//save file
			file_put_contents("app/".$data['controller']."/views/config/add.phtml", $file_add);
			file_put_contents("app/".$data['controller']."/views/config/edit.phtml", $file_edit);
			file_put_contents("app/".$data['controller']."/views/config/list.phtml", $file_list);
			file_put_contents("app/".$data['controller']."/views/config/details.phtml", $file_detail);
			
			
			/*------ Generate Table Estructure --------*/
			
			
			if($isNew){
			
				$this->xcodeModel->createTable($data['controller'],$this->_post());
				
				$this->message->setMsg('success', 'Modulo criado com sucesso!');
			
			} else {
				
				$this->xcodeModel->updateTable($data['controller'],$this->_post());	
				
				$this->message->setMsg('success', 'Modulo atualizado com sucesso!');
				
			}
			
			
		
		} else {
		
			$this->message->setMsg('error', 'Folder already exists');
		}


		
	}
	
	
	
	public function del(){
		
		
		if($this->_post('id')){
			
			$id = $this->_post('id');
			
			$controller = $this->_get('controller');
			
			$this->xcodeModel->del('id_controller = '.$id);
			
			$this->xcodeModel->drop('tb_'.$controller);
			
			$this->del_tree('app/'.$controller);			
			
		}
	
	}
	
	public function del_tree($dir) { 
	   $files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) { 
		  (is_dir("$dir/$file")) ? $this->del_tree("$dir/$file") : unlink("$dir/$file"); 
		} 
		return rmdir($dir); 
  	}
	
	
	
	
	//get the form fieds by type
	
	private function get_field_type($field, $value = null){
		


		switch($field['field_type']) {
			
			case "markup" :

				$_field =  $field['options']['markup'];
				
				break;
			
			case "textfield" :

				if( $value ) {				
					$value = '<?php print $view_data[0]["'.$field['field_machine_name'].'"];  ?>';
				}
				
				$_field =  '<input name="'.$field['field_machine_name'].'" type="'.$field['field_type'].'" value="'.$value.'" class="form-control" id="'.$field['field_machine_name'].'">';
				
				break;
			
			case "radio" :

				if( $value ) {				
					$value = '<?php print $view_data[0]["'.$field['field_machine_name'].'"];  ?>';
				}
				
				$_field  = '';
				
				$options = explode(PHP_EOL, $field['options']['radio']);
				
				foreach($options as $_option) {				
					if(empty($_option)) {
						unset($_option);
					} else {
						$opt = explode('|', $_option);
						
						$key = $opt[0];
						$val = $opt[1];
						
						$selected = '';
						if( $value ) {				
							$selected = '<?php if($view_data[0]["'.$field['field_machine_name'].'"] == "'.$key.'" ) print "checked";  ?>';
						}
						
						$_field  .= '<div class="radio-button"><input name="'.$field['field_machine_name'].'" type="radio" value="'.$key.'" id="'.$field['field_machine_name'].'" '.$selected.'/></div>';
						
						
					}
				}

				
				break;	
			
			case "checkbox" :

				if( $value ) {				
					$value = '<?php print $view_data[0]["'.$field['field_machine_name'].'"];  ?>';
				}
				
				$_field  = '';
				
				$options = explode(PHP_EOL, $field['options']['checkbox']);
				
				foreach($options as $_option) {				
					if(empty($_option)) {
						unset($_option);
					} else {
						$opt = explode('|', $_option);
						
						$key = $opt[0];
						$val = $opt[1];
						
						$selected = '';
						if( $value ) {				
							$selected = '<?php if($view_data[0]["'.$field['field_machine_name'].'"] == "'.$key.'" ) print "checked";  ?>';
						}
						
						$_field  .= '<div class="checkbox-button"><input name="'.$field['field_machine_name'].'" type="checkbox" value="'.$key.'" id="'.$field['field_machine_name'].'" '.$selected.'/></div>';
						
						
					}
				}

				
				break;
				
			case 'textarea' : 
				
				if( $value ) {				
					$value = '<?php print $view_data[0]["'.$field['field_machine_name'].'"];  ?>';
				}
				
				$_field =  '<textarea name="'.$field['field_machine_name'].'" class="form-control" id="'.$field['field_machine_name'].'">'.$value.'</textarea>';
				
				break;

			case 'select' :				
				
				$options = explode(PHP_EOL, $field['options']['select']);
				
				$values = '';
				
				$class='';$multiple ='';
				if($field['field_multiple_values'] == 'true') $class = "chosen-select";
				if(!empty($class)) $multiple = 'multiple';
				
				foreach($options as $_option) {				
					if(empty($_option)) {
						unset($_option);
					} else {
						$opt = explode('|', $_option);
						
						$key = $opt[0];
						$val = $opt[1];
						
						$selected = '';
						if( $value ) {				
							$selected = '<?php if($view_data[0]["'.$field['field_machine_name'].'"] == "'.$key.'" ) print "selected";  ?>';
						}
						
						$values .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
					}
				}
	
				$_field = '<select name="'.$field['field_machine_name'].'" class="form-control" id="'.$field['field_machine_name'].'" class="'.$class.'" '.$multiple.'> '.$values.' </select>';
				
				break;
				
			case 'taxonomy' :				
				
				$options = $field['options']['taxonomy']; //id do vocabulario				
				
				//precisa fazer um foreach buscando os termos de acordo com o vocabulario.
				$values = '';
							
				$_field = '';
				
				break;
				
			case 'entity' :	
				
				$options = explode('@', $field['options']['entity']);
				
				
				
				if($field['field_multiple_values'] == "true") {

					$selected = '';
						if( $value ) {	
							$selected = '<?php if(in_array($'.$options[0].'["id_'.$options[0].'"],$view_'.$field['field_machine_name'].')) print "selected";  ?>';
						}
					
					$_field = '<select name="'.$field['field_machine_name'].'[]" id="'.$field['field_machine_name'].'" class="form-control chosen-select" multiple>					  
						  <?php foreach($view_'.$options[0].' as $'.$options[0].'){?>
						  <option value="<?php print $'.$options[0].'["id_'.$options[0].'"] ?>" '.$selected.'><?php print $'.$options[0].'["'.$options[1].'"] ?></option>
							<?php } ?>
						  </select>';
					
				} else {

					$selected = '';
						if( $value ) {				
							$selected = '<?php if($view_data[0]["'.$field['field_machine_name'].'"] == $'.$options[0].'["id_'.$options[0].'"] ) print "selected";  ?>';
						}
						
					$_field = '<select name="'.$field['field_machine_name'].'" id="'.$field['field_machine_name'].'" class="form-control">
						  <option value="" selected="selected">Selecione</option>						  
						  <?php foreach($view_'.$options[0].' as $'.$options[0].'){?>
						  <option value="<?php print $'.$options[0].'["id_'.$options[0].'"] ?>" '.$selected.'><?php print $'.$options[0].'["'.$options[1].'"] ?></option>
							<?php } ?>
						  </select>';
					
				}
							
				break;
				
			case 'image' : 
				
				if( $value ) {				
					$value = '<div><img src="themes/files/uploads/<?php print $view_data[0]["'.$field['field_machine_name'].'"]; ?>" alt="" width="120" height="90" class="img-thumbnail" /></div>';
				}
				
				$_field =  '<input type="file" name="'.$field['field_machine_name'].'" id="'.$field['field_machine_name'].'" class="form-control" />'.$value;
				
				break;
				
			case 'file' : 
				
				if( $value ) {				
					$value = '<?php print $view_data[0]["'.$field['field_machine_name'].'"];?>';
				}
				
				$_field =  '<textarea name="'.$field['field_machine_name'].'" class="form-control" id="'.$field['field_machine_name'].'">'.$value.'</textarea>';
				
				break;
			
		}
		
		return $_field;
		
	}
	
	
	public function getOptions(){
		
		$dados['name'] = $this->_post('machine_name');
		$dados['field_type'] = $this->_post('field_type');
		
		if($dados['field_type'] == 'taxonomy') {
			
			include_once('app/taxonomia/models/VocabulariosModel.php');
			
			$vocabularios = new VocabulariosModel();
			
			$dados['data'] = $vocabularios->getAll();
		
			$this->viewNoBase('options/taxonomy', $dados);
		
		}
		
		if($dados['field_type'] == 'entity') {
			
			$current_value = $this->_post('current_value');
			$dados['current_value'] = explode('@',$current_value);

			$dados['field_multiple_values'] = $this->_post('field_multiple_values');
			
			$this->viewNoBase('options/entity', $dados);
		
		}
		
		if($dados['field_type'] == 'select'){			
			
			$this->viewNoBase('options/select', $dados);
		}
		
		if($dados['field_type'] == 'radio'){			
			
			$this->viewNoBase('options/radio', $dados);
		}
		
		if($dados['field_type'] == 'checkbox'){			
			
			$this->viewNoBase('options/checkbox', $dados);
		}
		
		if($dados['field_type'] == 'markup'){			
			
			$this->viewNoBase('options/markup', $dados);
		}
	
	
	}
	
	public function getControllerData($id = null){
		
		$id = $this->_post('id');
		
		$data = $this->xcodeModel->get($id);
		$data = $data[0];
		
		$fields = unserialize($data['data']);

		$html = '';
		
		foreach($fields['field'] as $field) {
			
			$html .= '<option data-controller="'.strtolower(functions::removeSpecialChars($data['controller'])).'" value="'.$field['field_machine_name'].'">'.$field['field_name'].'</option>'; 
			
		}
		
		print($html);
		
		die;
		
		
		
		
	}
	
	public function getControllerFields($id){		
	
		$data = $this->xcodeModel->get($id);
		$data = $data[0];
		
		$fields = unserialize($data['data']);
	
		return $fields;

		
	}
	
	


}

?>