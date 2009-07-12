<?php

class Ximi_Chunk {

  public $text;
  public $font;
  public $size;
  public $color;

  public $ImageProcessor;

  public $originX;
  public $originY;

  public $width;
  public $height;

  protected $wordBoundaries = array(
    ' ',
    '-',
  );

  public function __construct($text, $font, $size, $color, $ImageProcessor) {
    $this->text = $text;
    $this->font = $font;
    $this->size = $size;
    $this->color = $color;
    $this->ImageProcessor = $ImageProcessor;
    $this->setDimensions();
  }

  public function removeFirstWord() {
    $removedWord = substr($this->text, 0, $this->firstWordBoundary());
    $this->text = substr($this->text, $this->firstWordBoundary());
    $this->setDimensions();
    return $removedWord;
  }

  public function isMultiWord() {
    foreach ($this->wordBoundaries as $char) {
      if (strpos($this->text, $char) !== false) {
        return true;
      }
    }
    return false;
  }

  public function firstWordBoundary() {
    $firstSpace = strpos($this->text, ' ');
    if ($firstSpace === 0) {
      $firstSpace = strpos($this->text, ' ', 1);
    }
    $firstHyphen = strpos($this->text, '-');
    if ($firstHyphen === false) {
      return $firstSpace;
    } elseif ($firstSpace === false) {
      return $firstHyphen+1;
    } else {
      return min($firstSpace, $firstHyphen+1);
    }
  }

  public function firstWordWidth() {
    $firstWord = substr($this->text, 0, $this->firstWordBoundary());
    list($width) = $this->ImageProcessor->textBoundingBox($this->size, $this->font, $firstWord);
    return $width;
  }

  public function trimStart() {
    while (substr($this->text, 0, 1) == ' ') {
      $this->text = substr($this->text, 1);
    }
    $this->setDimensions();
  }

  public function trimEnd() {
    while (substr($this->text, -1) == ' ') {
      $this->text = substr($this->text, 0, -1);
    }
    $this->setDimensions();
  }

  public function setDimensions() {
    list(
      $this->width,
      $this->height,
      $this->originY
    ) = $this->ImageProcessor->textBoundingBox($this->size, $this->font, $this->text);
  }

  public function addToCanvas($canvas, $x, $y, $rotation) {
    $this->ImageProcessor->printText($canvas, $this->size, $rotation, $x, $y, $this->color, $this->font, $this->text, 100);
  }

}

?>
