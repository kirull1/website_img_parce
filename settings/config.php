<?php

    namespace setting;

    class db{

        protected static $config = [
            'db_type' => 'mysql', // Тип базы данных (По умолчанию mysql)
            'host' => 'localhost', // Адрес подключения
            'port' => 3306, // Порт для подключения (По умолчанию 3306)
            'dbname' => 'dbname', // Название базы данных
            'login' => 'login', // Логин для подключения
            'password' => 'password' // Пароль для подключения
        ];

    }

    class api{

        public static $config = [
            'domain' => 'your_domain_name', // Ваше доменное имя
            'start' => 20, // Начальное кол-во записей на главной странице
            'search_start' => 20, // Начальное кол-во записей при поиске
            'add' => 20, // Кол-во записей для промежудка
            'limit' => 10, // Ограничение на количество запросов к API
            'limit_time' => 10, // Ограничение на запрос в секунду к API
            'ip_check' => true // Проверка ip адреса (По умолчанию false)
        ];

    }

    
