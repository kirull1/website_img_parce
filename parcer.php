<?php

    /*
     *  Лицензии использования.
     *  http://imgpng.ru/license
     *  http://www.pngmart.com/copyright-policy
     *
     */

    require_once "settings/db.php";
    require_once "settings/image.php";

    use setting\transform;
    use setting\db_start;

    class image extends db_start {

        public function __construct(){
            $this->checkdir('image');
            $this->checkdir('image/full');
            $this->checkdir('image/small');

            parent::__construct(null);
        }

        private function checkdir($way){
            if (!file_exists($way)) return mkdir($way);
        }

        public static function get_image_pngimgcom(){
            $arr = array_map(function($e){
                return explode('</span>', $e);
            },explode('<a href=">', file_get_contents('http://imgpng.ru/')))[0];
        
            foreach($arr as $val){
                if(!strripos($val, 'div') && !strripos($val, 'new_image') && !strripos($val, 'com')){
                    $val = explode('<a href="',$val)[1];
                    foreach (array_unique(array_map(function($e){
                        return explode('"', $e)[0];
                    },explode('src="',file_get_contents('http://imgpng.ru/'.trim(explode('"', $val)[0], '/'))))) as $value) {
                        if(strripos($value, 'uploads')) @yield ['http://'.str_replace('small/', '', trim($value, '/')), trim(str_replace(['_', 'pngimg.com'], ' ',stristr(str_replace('/uploads/', '', trim($value, '/')), '/', true)))];
                    }
                }
            }
        }

        public static function get_image_pngmart($last){ // Количество страниц
            for ($i=1; $i <= $last; $i++) { 
                $arr = array_values(array_slice(array_unique(array_map(function($e){return explode('href="',$e)[1];},explode('rel="bookmark"', file_get_contents('http://www.pngmart.com/page/'.$i.'?s')))), 1, 16));
                foreach($arr as $val){
                    $get = file_get_contents('http://www.pngmart.com/image/'.preg_replace("/[^0-9]/", '', $val));
                    $str = explode('class="attachment-full', $get)[0];
                    $title = explode('</h1>',explode('<h1 class="entry-title">',$get)[1])[0];
                    @yield [trim(str_replace('src=', '', substr($str, strrpos($str, 'src'))), '" '), $title];
                }
            }
        }

        public static function save_img($url, $tags){
            $image = new db_start;
            $token = bin2hex(random_bytes(15)).'.png'; 
            $full = 'image/full/'.$token;
            if(file_put_contents($full, file_get_contents($url))){
                transform::squeeze($token, 'image/full/', 'image/small/', 140, 140, 9); // file_name, file_way_get, file_way_save, width, height, lvl_squeeze;
                $image->build_request($image->get_build('INSERT INTO `image` (`way`, `tags`, `origin`) VALUES (?, ?, ?)'), $token, $tags, $url);
            }
        }

    }