<?php

class Ximi_Image_GD implements Ximi_Image {
  protected $controlText = 'kp';
  public function createCanvas($width, $height, $color = null) {
    $canvas = imagecreatetruecolor($width, $height);
    if ($color == 'transparent') {
      imagealphablending($canvas, false);
      $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
      imagefilledrectangle($canvas, 0, 0, $width, $height, $transparent);
      imagecolortransparent($canvas, $transparent);
      imagesavealpha($canvas, true);
      imagealphablending($canvas, true);
    } else {
      $color = imagecolorallocate($canvas, $color[0], $color[1], $color[2]);
      imagefilledrectangle($canvas, 0, 0, $width, $height, $color);
    }
    return $canvas;
  }
  public function load($filename) {
    switch (strtolower(end($tmp = explode('.', $filename)))) {
      case 'gif':
        return imagecreatefromgif($filename);
        break;
      case 'png':
        return imagecreatefrompng($filename);
        break;
      case 'jpeg':
      case 'jpg':
        return imagecreatefromjpeg($filename);
        break;
      default:
        return false;
        break;
    }
  }
  public function dimensions($image) {
    return array(imagesx($image), imagesy($image));
  }
  public function addBackground($canvas, $bgImage, $bgWidth, $bgHeight, $bgOriginX, $bgOriginY) {
    $background = imagecreatetruecolor($bgWidth, $bgHeight);
    imagesettile($background, $bgImage);
    imagefill($background, 0, 0, IMG_COLOR_TILED);
    imagecopy($canvas, $background, $bgOriginX, $bgOriginY, 0, 0, $bgWidth, $bgHeight);
  }
  public function textBoundingBox($size, $font, $text) {
    $controlBox = imagettfbbox($size, 0, $font, $this->controlText);
    $controlLeft = min($controlBox[6], $controlBox[0]);
    $controlRight = max($controlBox[4], $controlBox[2]);
    $controlTop = min($controlBox[7], $controlBox[5]);
    $controlBottom = max($controlBox[1], $controlBox[3]);
    $box = imagettfbbox($size, 0, $font, $text);
    $left = min($box[6], $box[0]);
    $right = max($box[4], $box[2]);
    $top = min($box[7], $box[5]);
    $bottom = max($box[1], $box[3]);
    $width = $right - $left;
    $height = max($bottom - $top, $controlBottom - $controlTop);
    $baseline = abs(min($top, $controlTop));
    return array(
      $width,
      $height,
      $baseline
    );
  }
  public function printText($canvas, $size, $angle, $x, $y, $color, $fontfile, $text, $opacity = 100) {
    $alpha = 127 - (127 * $opacity / 100);
    $color = imagecolorallocatealpha($canvas, $color[0], $color[1], $color[2], $alpha);
    return imagettftext($canvas, $size, $angle, $x, $y, $color, $fontfile, $text);
  }
  public function resize($original, $newWidth, $newHeight) {
    list($width, $height) = $this->dimensions($original);
    $resized = imagecreatetruecolor($newWidth, $newHeight);
    imagealphablending($resized, false);
    imagesavealpha($resized, true);
    imagecopyresampled($resized, $original, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    return $resized;
  }
  public function rotate($image, $degrees, $color) {
//    die(pr(gd_info()));
    if ($color == 'transparent') {
//      imagealphablending($image, false);
      $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
//      imagecolortransparent($image, $color);
//      imagesavealpha($image, true);
//      imagealphablending($image, true);
    } else {
      $color = imagecolorallocate($image, $color[0], $color[1], $color[2]);
    }
    return imagerotate($image, $degrees, $color, 1);
  }
  public function render($image) {
    imagepng($image);
  }
}

?>
