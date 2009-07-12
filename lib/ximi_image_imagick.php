<?php

class Ximi_Image_Imagick implements Ximi_Image {
  protected $fontSizeScale = 1.3;
  protected $lineHeightScale = 0.85;
  protected static function rgb($color) {
    return 'rgb('.implode(',', $color).')';
  }
  public function createCanvas($width, $height, $color = null) {
    $canvas = new Imagick();
    if ($color == 'transparent') {
      $bg = new ImagickPixel('transparent');
    } else {
      $bg = new ImagickPixel(self::rgb($color));
    }
    $canvas->newImage($width, $height, $bg);
    $canvas->setImageFormat('png');
    return $canvas;
  }
  public function load($filename) {
    $img = new Imagick();
    $img->readImageBlob(file_get_contents($filename));
    return $img;
  }
  public function dimensions($image) {
    return array($image->getImageWidth(), $image->getImageHeight());
  }
  public function addBackground($canvas, $bgImage, $bgWidth, $bgHeight, $bgOriginX, $bgOriginY) {
    $bg = $this->tile($bgImage, $bgWidth, $bgHeight);
    $canvas->compositeImage($bg, Imagick::COMPOSITE_DEFAULT, $bgOriginX, $bgOriginY, Imagick::CHANNEL_ALL);
  }
  protected function tile($tile, $width, $height) {
    $canvas = new Imagick();
    $canvas->newImage($width, $height, 'transparent');
    list($tileWidth, $tileHeight) = $this->dimensions($tile);
    $x = $y = 0;
    while ($y <= $height) {
      while ($x <= $width) {
        $canvas->compositeImage($tile, Imagick::COMPOSITE_DEFAULT, $x, $y, Imagick::CHANNEL_ALL);
        $x += $tileWidth;
      }
      $y += $tileHeight;
      $x = 0;
    }
    return $canvas;
  }
  public function textBoundingBox($size, $font, $text) {
    $im = new Imagick();
    $draw = new ImagickDraw();
    $draw->setFontSize($size*$this->fontSizeScale);
    $draw->setFont($font);
    $fontMetrics = $im->queryFontMetrics($draw, $text);
    return array(
      $fontMetrics['textWidth'],
      $fontMetrics['textHeight']*$this->lineHeightScale,
      $fontMetrics['ascender']*$this->lineHeightScale
    );
  }
  public function printText($canvas, $size, $angle, $x, $y, $color, $fontfile, $text, $opacity = 100) {
    $draw = new ImagickDraw();
    $draw->setFillColor(self::rgb($color));
    $draw->setFontSize($size*$this->fontSizeScale);
    $draw->setFont($fontfile);
    $draw->setFillOpacity($opacity/100);
    $canvas->annotateImage($draw, $x, $y, $angle, $text);
  }
  public function resize($original, $newWidth, $newHeight) {
    $original->thumbnailimage($newWidth, $newHeight);
    return $original;
  }
  public function rotate($image, $degrees, $color) {
    if ($color == 'transparent') {
      $bg = new ImagickPixel('transparent');
    } else {
      $bg = new ImagickPixel(self::rgb($color));
    }
    $image->rotateImage($bg, -$degrees);
    return $image;
  }
  public function render($image) {
    echo $image;
  }
}

?>
