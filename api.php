<?php

	require_once "settings/db.php";

	use setting\db_start;

	class ApiException extends Exception{
		public function __construct($message = null, $code = 500){
			parent::__construct($message, $code);
		}
	} 

	class api extends db_start{

		public function __construct($default, $settings_pdo = null){
			$this->start = $default['start'];
			$this->search_start = $default['search_start'];
			$this->add = $default['add'];
			$this->limit = $default['limit'];
			$this->limit_time = $default['limit_time'];

			parent::__construct($settings_pdo);

			setting\api::$config['ip_check'] === false ?: $this->ip_сheck();
		}

		// Получение значений
		public function get_data(){
			$last = self::build_request($this->get_build('SELECT * from `image` ORDER BY `id` ASC LIMIT 1'));
			return $last;
		}

		// Проверка ip адреса
		public function ip_сheck(){
			$ip = self::build_request($this->get_build('SELECT * from `ip_log` WHERE `ip` = ? AND `time` > ? ORDER BY `id` DESC'), $_SERVER['REMOTE_ADDR'], time() - $this->limit_time);
			if (empty($ip) || count($ip) < $this->limit) {
				return self::build_request($this->get_build('INSERT INTO `ip_log` (`ip`, `time`) VALUES (?, ?)'), $_SERVER['REMOTE_ADDR'], time());
			} else throw new ApiException('Слишком много запросов!', 429);
		}

	}

	try {
		if (isset($_GET['id']) && isset($_GET['type']) && isset($_GET['message'])) {

			$default = setting\api::$config;

			$Api = new api($default);

				if ($_GET['type'] == 'index') { // Главная страница

					switch ($_GET['message']) {
						case 'FIRST':
							$db = $Api->get_build('SELECT * from `image` ORDER BY `id` DESC LIMIT ?'); // Получения ограниченного кол-во записей
							$Message = $Api->build_request($db, [$default['start'], PDO::PARAM_INT]);
							break;

						case 'ADD':
							if (is_numeric($_GET['id'])) {
								$db = $Api->get_build('SELECT * from `image` ORDER BY `id` DESC LIMIT ?,?'); // Получения ограниченного промежудка записей
								$Message = $Api->build_request($db, [$_GET['id'] + $default['start'], PDO::PARAM_INT], [$default['add'], PDO::PARAM_INT]);
							} else throw new ApiException('Параметр id указан неверно!', 400);
							break;

						default:
							throw new ApiException('Некоторые параметры указаны неверно!', 400); // Исключение
					}
				} elseif ($_GET['type'] == 'search') { // Поиск
					switch ($_GET['message']) {
						case 'FIRST':
							$db = $Api->get_build('SELECT * from `image` WHERE `tags` LIKE ? ORDER BY `id` DESC LIMIT ?'); // Получения ограниченного количества записей для поиска
							$Message = $Api->build_request($db, "%{$_GET['search']}%", [$default['search_start'], PDO::PARAM_INT]);
							break;

						case 'ADD':
							if (is_numeric($_GET['id'])) {
								$db = $Api->get_build('SELECT * from `image` WHERE `tags` LIKE ? LIMIT ?,?'); // Получения ограниченного промежудка записей для поиска
								$Message = $Api->build_request($db, "%{$_GET['search']}%", [$_GET['id'], PDO::PARAM_INT], [$default['add'], PDO::PARAM_INT]);
							} else throw new ApiException('Параметр id указан неверно!', 400);
							break;

						default:
							throw new ApiException('Некоторые параметры указаны неверно!', 400);
					}
				} elseif ($_GET['type'] == 'stats') {
					$db = $Api->get_build('SELECT count(*) AS `count` from `image`'); // Получен\ статистики
					$Message = $Api->build_request($db);
					$Message[0]['less'] = $default['add']; 
				} else throw new ApiException('Тип указан не верно!', 400);

				http_response_code(200);
				die(json_encode(['Error' => false, 'Message' => $Message, 'Last' => $last ?: 1])); // Удачный ответ
		} else throw new ApiException('Параметры отсутствуют!', 400);

	}catch (PDOException | ApiException $e) {
		http_response_code($e->getCode() ?: 500);
		die(json_encode(['Error' => true, 'Message' => $e->getMessage()])); // Проверка исключений
	}
