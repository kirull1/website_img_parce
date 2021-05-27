<?php

    namespace setting;

    use setting\db;

    require_once "config.php";

    class db_start extends db{

        public function __construct($settings_pdo = null){
            if($settings_pdo === null) $settings_pdo = parent::$config;
            
            $this->db_type = $settings_pdo['db_type'] ?: 'mysql';
            $this->host = $settings_pdo['host'];
            $this->port = $settings_pdo['port'] ?: 3306;
            $this->dbname = $settings_pdo['dbname'];
            $this->login = $settings_pdo['login'];
            $this->password = $settings_pdo['password'];

            $this->check_db();
        }

        // Проверка на наличее таблиц
        private function check_db(){
            $encoding = 'utf8mb4_unicode_ci';
            if(!$this->get_build('DESCRIBE `image`')->execute()){
                $this->get_build("CREATE TABLE `image` (
                    `id` int(11) NOT NULL,
                    `way` varchar(255) COLLATE $encoding NOT NULL,
                    `tags` text COLLATE $encoding NOT NULL,
                    `origin` text COLLATE $encoding
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=$encoding;")->execute();
                $this->get_build('ALTER TABLE `image` ADD PRIMARY KEY (`id`);')->execute();
                $this->get_build('ALTER TABLE `image` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;')->execute();
            }
            if(!$this->get_build('DESCRIBE `ip_log`')->execute()){
                $this->get_build("CREATE TABLE `ip_log` (
                    `id` int(11) NOT NULL,
                    `ip` varchar(20) COLLATE $encoding NOT NULL,
                    `time` int(11) NOT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=$encoding;")->execute();
                $this->get_build('ALTER TABLE `ip_log` ADD PRIMARY KEY (`id`);')->execute();
                $this->get_build('ALTER TABLE `ip_log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT; COMMIT')->execute();
            }
        }

        // Подключения к базе данных
        protected function connect_pdo(){ // host, login, password
            return new \PDO("{$this->db_type}:host={$this->host};port={$this->port};dbname={$this->dbname}", $this->login, $this->password);
        }

        // Создание запроса
		public function get_build($rows){
			return $this->connect_pdo()->prepare($rows);
		}

		// Постройка запроса к бд
		public static function build_request($rows, ...$array){
			$rows->setFetchMode(\PDO::FETCH_ASSOC);
			foreach($array as $key => $arr)
				if (is_array($arr) && is_int($key))
					if (count($arr) == 2) 
						$rows->bindValue($key+1, $arr[0], $arr[1]); else throw new \PDOException('Ошибка в указании параметров!');
				else $rows->bindValue($key+1, $arr);
			$rows->execute();
			foreach ($rows as $row) $value[] = $row;
			return $value ?: false;
		}
    }