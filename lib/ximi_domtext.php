<?php

class Ximi_DOMText extends DOMText {

  public function getNodeValue() {
    switch ($this->parentNode->style['text-transform']) {
      case 'capitalize':
        return ucwords($this->nodeValue);
        break;
      case 'uppercase':
        return strtoupper($this->nodeValue);
        break;
      case 'lowercase':
        return strtolower($this->nodeValue);
        break;
      default:
        return $this->nodeValue;
        break;
    }
  }

}

?>
