<?php

    set_time_limit(0);

    require_once "settings/db.php";
    require_once "settings/image.php";

    use setting\transform;
    use setting\db_start;

    class image extends db_start {

        public static function get_image(){
            $arr = array_map(function($e){
                return explode('</span>', $e);
            },explode('<a href=">', file_get_contents('http://pngimg.com/')))[0];
        
            foreach($arr as $val){
                if(!strripos($val, 'div') && !strripos($val, 'new_image') && !strripos($val, 'com')){
                    $val = explode('<a href="',$val)[1];
                    foreach (array_unique(array_map(function($e){
                        return explode('"', $e)[0];
                    },explode('src="',file_get_contents('http://pngimg.com/'.trim(explode('"', $val)[0], '/'))))) as $value) {
                        if(strripos($value, 'uploads')) @yield $value;
                    }
                }
            }
        }

    }

    $image = new image();
    foreach ($image::get_image() as $value){
        $token = bin2hex(random_bytes(15)).'.png'; 
        $full = file_get_contents('image/full/'.$token);
        if(file_put_contents($full, file_get_contents('http://pngimg.com'.str_replace('small/', '', $value)))){
            transform::squeeze($full, 'image/full/', 'image/small/', 180, 180, 9); // file_name, file_way_get, file_way_save, width, height, lvl_squeeze;
            $image->build_request($image->get_build('INSERT INTO `image` (`way`, `tags`) VALUES (?, ?)'), $token, str_replace('_', ' ',stristr(str_replace('/uploads/', '',$value), '/', true)));
        }
    }
