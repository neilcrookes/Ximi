<?php

class Ximi_DOMElement extends DOMElement {

  public $style = array(
    'font-family' => 'arial',
    'font-size' => '10pt',
    'font-weight' => 'normal',
    'font-style' => 'normal',
    'font-variant' => 'normal',
    'letter-spacing' => 'normal',
    'word-spacing' => 'normal',
    'line-height' => 'normal',
    'text-decoration' => 'none',
    'text-transform' => 'none',
    'text-shadow' => 'none',
    'white-space' => 'normal',
    'direction' => 'ltr',
    'unicode-bidi' => 'normal',
    'color' => '#000000',
  );

  public function setStyle($defaultInnerStyles = array()) {
    $this->style = array_merge($this->style, $defaultInnerStyles);
    if (!$this->hasAttribute('style')
    || !preg_match_all('/\s*([^:;]+)\s*:\s*([^;]+)\s*/', $this->getAttribute('style'), $matches)) {
      return;
    }
    $style = array_combine($matches[1], $matches[2]);
    $style = array_intersect_key($style, $this->style);
    $this->style = array_merge($this->style, $style);
  }
}

?>
