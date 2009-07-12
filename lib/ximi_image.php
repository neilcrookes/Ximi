<?php

interface Ximi_Image {
  public function createCanvas($width, $height, $color = null);
  public function load($filename);
  public function dimensions($image);
  public function addBackground($canvas, $bgImage, $bgWidth, $bgHeight, $bgOriginX, $bgOriginY);
  public function textBoundingBox($size, $font, $text);
  public function printText($image, $size, $angle, $x, $y, $color, $fontfile, $text, $opacity = 100);
}

?>
