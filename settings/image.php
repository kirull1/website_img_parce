<?php

    namespace setting;

    class transform{

        public static function squeeze($file, $get, $save, $width, $height, $quality = 0){
            list($width_old, $height_old) = getimagesize("../image/full/$file");
            $image_background = imagecreatetruecolor($width, $height);
            imagealphablending($image_background, false);
            imagesavealpha($image_background, true);
            $image = imagecreatefrompng("$get/$file");
            imagecopyresampled($image_background, $image, 0, 0, 0, 0, $width, $height, $width_old, $height_old);
            return imagepng($image_background, "$save/$file", $quality);
        } 

    }