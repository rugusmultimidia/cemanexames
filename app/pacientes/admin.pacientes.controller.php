<?php

class pacientes extends Controller {

	public function init() {
		$this->pacientes_model = new pacientesModel();
		$this->exames_model = new examesModel();
		//$this->add_js('app/pacientes/assets/pacientes.js');
	}

	public function index_action() {
		$dados = array();

		$id_responsavel = $this->_get('paciente');
		if (is_numeric($id_responsavel)) {
			$dados['responsavel'] = $this->pacientes_model->get($id_responsavel);
		}

		$this->q = (isset($_GET['q']) ? $_GET['q'] : "");

		$this->pagination = new Pagination();
		$this->pagination->defineQryString("?q=" . $this->q);

		$count = $this->pacientes_model->getSearch($this->q);
		$this->pagination->link('admin/pacientes/index/page');
		$this->pagination->setpaginate(count($count), ih_ItemsPerPage, ih_visibleItems, $this->_get('page'));

		$id_responsavel = $this->_get('paciente');
		
		if (is_null($id_responsavel)) {
			$dados['list'] = $this->pacientes_model->getSearch($this->q, $this->pagination->getLimit());
			// $this->printar($dados['list']);
			$this->view('config/list', $dados);
		} else {
			$dados['list'] = $this->pacientes_model->getSearch($this->q, $this->pagination->getLimit(), $id_responsavel);
			$this->view('config/list_dependentes', $dados);
		}
	}

	public function list_exames() {
		$cod = $this->_get('id');
		$dados = array();

		$this->pagination = new Pagination();
		$count = $this->exames_model->getIdPaciente($cod);
		$this->pagination->link('admin/pacientes/index/page');
		$this->pagination->setpaginate(count($count), ih_ItemsPerPage, ih_visibleItems, $this->_get('page'));

		$dados['list'] = $this->exames_model->getIdPaciente($cod, $this->pagination->getLimit());
		$dados['paciente'] = $this->pacientes_model->get($cod);

		// $this->printar($dados);
		// die('exames');
		$this->view('exames_paciente', $dados);
	}

	public function resultado_exames() {

		header('Content-Type: application/json');
		http_response_code(200);

		$id_pacientes = $_SESSION['@paciente']['id_pacientes'];
		// die(var_dump($id_pacientes));
		$dados = array();

		if (is_numeric($id_pacientes)) {
			$dados['paciente'] = $_SESSION['@paciente'];
			$dados['exames'] = $this->exames_model->getIdPacienteResultSite($id_pacientes);

			foreach ($dados['exames'] as &$exame) {
				if (isset($exame['pdf']) && !empty($exame['pdf'])) {
					
					$exame['pdf'] = unserialize($exame['pdf']);
					
					foreach ($exame['pdf'] as &$pdf) {

						$file_pdf = $pdf['file'];
						$file_path = 'themes/files/uploads/' . $file_pdf;

						if (file_exists($file_path)) {
							$file = 'themes/files/uploads/' . $file_pdf;
						} else {
							$file = 'https://examesceman.s3.amazonaws.com/uploads/' . $file_pdf;
						}
						$pdf['file'] = $file;
					}

				}


				if (isset($exame['dados']) && !empty($exame['dados'])) {
					$exame['dados'] = unserialize($exame['dados']);
				}
			}
		}

		echo json_encode($dados, JSON_UNESCAPED_UNICODE);

		session_destroy();
		unset($_SESSION);
		die();

	}

	public function add() {
		$id_responsavel = $this->_get('paciente');

		if ($this->_post()) {
			
			$dataSave = array(
				"nome" => html_entity_decode($this->_post("nome"), ENT_QUOTES, 'UTF-8'),
				"codigo_paciente" => html_entity_decode($this->_post("codigo_paciente"), ENT_QUOTES, 'UTF-8'),
				"senha" => rand(100000, 999999),
				"data_nascimento" => html_entity_decode($this->validateAndFormatDate($this->_post("data_nascimento")), ENT_QUOTES, 'UTF-8'),
				"email" => html_entity_decode($this->_post("email"), ENT_QUOTES, 'UTF-8'),
				"cpf" => $this->cleanCPF(html_entity_decode($this->_post("cpf"), ENT_QUOTES, 'UTF-8')),
				"telefone" => html_entity_decode($this->_post("telefone"), ENT_QUOTES, 'UTF-8'),
				"celular" => html_entity_decode($this->_post("celular"), ENT_QUOTES, 'UTF-8'),
				"clinica" => html_entity_decode($this->clinica(), ENT_QUOTES, 'UTF-8'),
				'id_user' => $this->init->user['id_user'],
				'date_created' => date('Y-m-d H:i'),
				'date_update' => date('Y-m-d H:i')
			);

			// $this->printar($dataSave);

			if (is_numeric($id_responsavel)) {
				$dataSave['id_responsavel'] = $id_responsavel;
			}

			if ($this->_post('id_pacientes')) {
				unset($dataSave['date_created']);
				$this->pacientes_model->edit($dataSave, 'id_pacientes = ' . $this->_post('id_pacientes'));
				header('Location: /admin/pacientes/edit/id/' . $this->_post('id_pacientes'));
				die();
			} else {
				$this->pacientes_model->save($dataSave);
				$this->message->setMsg('success', 'Salvo com sucesso.');
			}

		}

		$dados = array();

		if (is_numeric($id_responsavel)) {
			$dados['responsavel'] = $this->pacientes_model->get($id_responsavel);
		}

		$this->view('config/add', $dados);
	}

	public function details() {
		$id = $this->_get('id');
		$dados = array();
		$dados['data'] = $this->pacientes_model->get($id);
		$this->view('config/details', $dados);
	}

	public function edit() {
		$id = $this->_get('id');

		if ($this->_post()) {

			
			$dataSave = array(
				"nome" => html_entity_decode($this->_post("nome"), ENT_QUOTES, 'UTF-8'),
				"codigo_paciente" => html_entity_decode($this->_post("codigo_paciente"), ENT_QUOTES, 'UTF-8'),
				"senha" => html_entity_decode($this->_post("senha"), ENT_QUOTES, 'UTF-8'),
				"email" => html_entity_decode($this->_post("email"), ENT_QUOTES, 'UTF-8'),
				"cpf" => $this->cleanCPF(html_entity_decode($this->_post("cpf"), ENT_QUOTES, 'UTF-8')),
				"telefone" => html_entity_decode($this->_post("telefone"), ENT_QUOTES, 'UTF-8'),
				"celular" => html_entity_decode($this->_post("celular"), ENT_QUOTES, 'UTF-8'),
				'id_user' => $this->init->user['id_user'],
				'date_update' => date('Y-m-d H:i')
			);


			$dataSave['data_nascimento'] = $this->validateAndFormatDate($this->_post("data_nascimento"));
			
			// $this->printar($dataSave);

			$this->pacientes_model->edit($dataSave, 'id_pacientes = ' . $id);
			$this->message->setMsg('success', 'Editado com sucesso.');
		}

		$dados = array();
		$dados['data'] = $this->pacientes_model->get($id);
		$this->view('config/edit', $dados);
	}

	public function convertDateFormats() {
		$pacientes = $this->pacientes_model->getAll();

		foreach ($pacientes as $paciente) {
			$data_nascimento = $paciente['data_nascimento'];

			if ($this->isDateBR($data_nascimento)) {
				$data_nascimento = $this->convertDateToUSA($data_nascimento);
			}

			$dataSave = array(
				"data_nascimento" => $data_nascimento,
				'date_update' => date('Y-m-d H:i')
			);

			$this->pacientes_model->edit($dataSave, 'id_pacientes = ' . $paciente['id_pacientes']);
		}
	}

	private function isDateBR($date) {
		$d = DateTime::createFromFormat('d/m/Y', $date);
		return $d && $d->format('d/m/Y') === $date;
	}

	private function convertDateToUSA($date) {
		$d = DateTime::createFromFormat('d/m/Y', $date);
		return $d->format('Y-m-d');
	}

	public function del() {
		if ($this->_post('id')) {
			$id = $this->_post('id');
			
			$dataSave = array(
				"status" =>"apagado",
				'id_user' => $this->init->user['id_user'],
				'date_update' => date('Y-m-d H:i')
			);

			$this->pacientes_model->edit($dataSave, 'id_pacientes = ' . $id);
		}
	}

	public function getPacienteAjax() {
		$nameSearch = $this->_post('term');
		$data = $this->pacientes_model->getSearch($nameSearch);
		print json_encode($data);
	}


	public function checkCpfAjax() {
		$cpf = $this->cleanCPF($this->_post('cpf'));
		$data = $this->pacientes_model->checkCPF($cpf);
		print json_encode($data);
	}

	public function addPatient() {
		if ($this->_post()) {
			$dataSave = array(
				"nome" => $this->_post("namePatient"),
				"data_nascimento" => $this->_post("dataPatient"),
				"cpf" => $this->_post("cpfPatient"),
				"num_cartao" => $this->_post("numCartaoPatient"),
				"senha" => rand(100000, 999999),
				"id_responsavel" => $this->_post("savePatient"),
				'id_user' => $this->init->user['id_user'],
				'date_created' => date('Y-m-d H:i'),
				'date_update' => date('Y-m-d H:i')
			);

			$this->pacientes_model->save($dataSave);
			$this->message->setMsg('success', 'Salvo com sucesso.');
		}

		$dados = array();
		$this->view('config/edit', $dados);
	}
}
?>
