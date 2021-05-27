<?php

    namespace setting;

    class db{

        protected static $config = [
            'db_type' => 'mysql', // Тип базы данных (По умолчанию mysql)
            'host' => 'localhost', // Адрес подключения
            'port' => 3306, // Порт для подключения (По умолчанию 3306)
            'dbname' => 'u1390296_default', // Название базы данных
            'login' => 'u1390296_default', // Логин для подключения
            'password' => 'c10ImbCEK1jiFDR8' // Пароль для подключения
        ];

    }

    class api{

        public static $config = [
            'domain' => 'u1390296.isp.regruhosting.ru', // Ваше доменное имя
            'start' => 20, // Начальное кол-во записей на главной странице
            'search_start' => 20, // Начальное кол-во записей при поиске
            'add' => 20, // Кол-во записей для промежудка
            'limit' => 10, // Ограничение на количество запросов к API
            'limit_time' => 10, // Ограничение на запрос в секунду к API
            'ip_check' => true // Проверка ip адреса (По умолчанию false)
        ];

    }

    