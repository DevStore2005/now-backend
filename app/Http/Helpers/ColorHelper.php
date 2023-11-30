<?php

namespace App\Http\Helpers;

class ColorHelper
{
    // get image color
    public function getImageColor($image)
    {
        $image = asset($image);
        $imageInfo = explode('.', $image);
        $extension = end($imageInfo);
        if ($extension) {
            switch ($extension) {
                case 'png':
                    $image = imagecreatefrompng($image);
                    break;
                case 'jpeg':
                    $image = imagecreatefromjpeg($image);
                    break;
                case 'jpg':
                    $image = imagecreatefromjpeg($image);
                    break;
                default:
                    return 'white';
            }
        } else {
            return 'white';
        }
        $color = imagecolorat($image, 1, 1);
        $color = imagecolorsforindex($image, $color);
        $color = $color['red'] . ',' . $color['green'] . ',' . $color['blue'];
        return $this->getApproximateColor($color);
    }

    // get approximate color black or white
    private function getApproximateColor($color)
    {
        $color = explode(',', $color);
        $red = $color[0];
        $green = $color[1];
        $blue = $color[2];
        $brightness = (($red * 299) + ($green * 587) + ($blue * 114)) / 1000;
        return ($brightness > 130) ? 'black' : 'white';
    }
}
