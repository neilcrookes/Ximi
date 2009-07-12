<?php

class Ximi_Line {
  public $Chunks = array();
  public $finalWidth = 0;
  public $finalHeight = 0;
  public $finalOriginY = 0;
  public $finalOriginX = 0;
  public function widthOfChunks() {
    $widthOfChunks = 0;
    foreach ($this->Chunks as $chunk) {
      $widthOfChunks += $chunk->width;
    }
    return $widthOfChunks;
  }
  public function setFinalOriginX($align, $canvasWidth) {
    switch (strtolower($align)) {
      case 'center':
        $this->finalOriginX = ($canvasWidth - $this->finalWidth) / 2;
        break;
      case 'right':
        $this->finalOriginX = $canvasWidth - $this->finalWidth;
        break;
      default:
        break;
    }
  }
}

?>
