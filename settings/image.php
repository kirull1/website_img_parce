<?php

    namespace setting;

    class transform{

        public static function squeeze($file, $get, $save, $width, $height, $quality = 0){
            list($width_old, $height_old) = @getimagesize(rtrim($get, '/')."/$file");
            $image_background = @imagecreatetruecolor($width, $height);
            $image = @imagecreatefrompng(rtrim($get, '/')."/$file");
            @imagealphablending($image_background, false);
            @imagesavealpha($image_background, true);
            @imageinterlace($image_background, 1);
            @imagecopyresampled($image_background, $image, 0, 0, 0, 0, $width, $height, $width_old, $height_old);
            @imagepng($image_background, rtrim($save, '/')."/$file", $quality);
            @imagedestroy($image_background);
            @imagedestroy($image);
        } 

    }