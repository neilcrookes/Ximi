<?php

function __autoload($class) {
  require_once('lib/'.strtolower($class).'.php');
}

class Ximi {

  protected $html = '<span />';

  protected $options = array();

  protected $allowedOptions = array(
    'processor', 'clip', 'text-align', 'vertical-align', 'width', 'height',
    'line-height', 'background-color', 'background-image',
    'background-position', 'background-repeat', 'rotation', 'padding-top',
    'padding-right', 'padding-bottom', 'padding-left', 'text-shadow'
  );

  protected $defaults = array(
    'width' => null,
    'height' => null,
    'clip' => 'both',
    'text-align' => 'left',
    'vertical-align' => 'top',
    'padding-top' => 0,
    'padding-right' => 0,
    'padding-bottom' => 0,
    'padding-left' => 0,
    'background-color' => 'transparent',
    'background-image' => null,
    'background-position' => '0 0',
    'background-repeat' => 'repeat',
    'background-attachment' => 'scroll',
    'line-height' => 1.2,
    'rotation' => 0,
    'processor' => 'GD',
  );

  protected $defaultInnerStyles = array(
    'color' => 'black',
  );

  protected $settings = array();

  protected $clipModes = array('width', 'height', 'both', 'none');
  protected $textAlignModes = array('left','center','right');
  protected $verticalAlignModes = array('top','middle','bottom');
  protected $processors = array('GD' => 'Ximi_Image_GD', 'IM' => 'Ximi_Image_Imagick');

  protected $Chunks = array();
  protected $Lines = array();

  protected $maxLineWidth = 0;
  protected $totalLineHeight = 0;
  protected $yOffset = 0;

  protected $Canvas = null;

  protected $canvasWidth = 0;
  protected $canvasHeight = 0;
  protected $shadows = array(
    array(
      'x' => 0,
      'y' => 0,
      'blur' => 5,
      'color' => array(255, 0, 0),
    ),
  );

  public function __construct($html = '', $options = array()) {

    $this->html = $html;

    $this->javascript2html();

    $this->options = $options;

    $this->validateOptions();

    if (isset($options['color'])) {
      $this->defaultInnerStyles['color'] = $options['color'];
      unset($options['color']);
    }

    $doc = DOMDocument::loadXML($this->html);
    $doc->registerNodeClass('DOMElement', 'Ximi_DOMElement');
    $doc->registerNodeClass('DOMText', 'Ximi_DOMText');

    /**
     * Call setChunks (recursive) method passing it the top level children of
     * the document.
     */
    $this->setChunks($doc->childNodes);

    $this->wordwrap = (boolean)$this->settings['width'];
    $this->setLines();
    $this->processLines();
    $this->ImageProcessor = new $this->processors[$this->settings['processor']]();
    $this->createCanvas();
    $this->addBackground();
    $this->printChunks();
//    $this->rotate();
    $this->render();
    
  }

  protected function javascript2html() {
    preg_match_all('/%u([0-9A-F]{4})/i', $this->html, $matches);
    if (!empty($matches)) {
      $count = count($matches[0]);
      for ($i=0; $i < $count; $i++) {
        $this->html = str_replace($matches[0][$i], '&#'.hexdec($matches[1][$i]).';', $this->html);
      }
    }
  }

  protected function validateOptions() {

    $validOptions = array_intersect_key($this->options, array_flip($this->allowedOptions));

    foreach ($validOptions as $option => $value) {
      if (empty($value)) {
        unset($validOptions[$option]);
        continue;
      }
      $validateMethod = str_replace(' ', '', 'validate'.ucwords(str_replace('-', ' ', $option)));
      if (method_exists($this, $validateMethod)) {
        $normalisedResult = $this->$validateMethod($value);
        if ($normalisedResult === false) {
          unset($validOptions[$option]);
          continue;
        } elseif ($normalisedResult !== true) {
          $validOptions[$option] = $normalisedResult;
        }
      }
    }
    $this->settings = array_merge($this->defaults, $validOptions);

  }

  protected function validateProcessor($value) {
    return array_key_exists($value, $this->processors);
  }
  protected function validateClip($value) {
    return in_array($value, $this->clipModes);
  }
  protected function validateTextAlign($value) {
    return in_array($value, $this->textAlignModes);
  }
  protected function validateVerticalAlign($value) {
    return in_array($value, $this->verticalAlignModes);
  }
  protected function validateWidth($value) {
    return is_numeric($value);
  }
  protected function validateHeight($value) {
    return is_numeric($value);
  }
  protected function validateLineHeight($value) {
    return is_numeric($value);
  }
  protected function validateBackgroundColor($value) {
    if ($value == 'transparent') {
      return true;
    }
    return Ximi_Color::rgb($value);
  }
  protected function validateRotation($value) {
    return is_numeric($value) && $value >= 0 && $value <= 360;
  }
  protected function validateTextShadow($value) {
    if (!preg_match_all('/([\dA-F]{3,6})\s(\d+)px\s(\d+)px\s(\d+)px/i', $value, $matches)) {
      return false;
    }
    $return = array();
    for ($i=0; $i<count($matches[0]); $i++) {
      $return[] = array(
        'x' => $matches[2][$i],
        'y' => $matches[3][$i],
        'blur' => $matches[4][$i],
        'color' => Ximi_Color::rgb($matches[1][$i]),
      );
    }
    return $return;
  }

  protected function setChunks($nodes) {
    foreach ($nodes as $node) {
      switch ($node->nodeType) {
        case XML_ELEMENT_NODE:
          if ($node->parentNode instanceof Ximi_DOMElement) {
            $node->style = $node->parentNode->style;
          }
          switch (strtolower($node->nodeName)) {
            case 'b':
            case 'strong':
              $node->style['font-weight'] = 'bold';
              break;
            case 'i':
            case 'em':
              $node->style['font-style'] = 'italic';
              break;
            case 'u':
              $node->style['text-decoration'] = 'underline';
              break;
            case 's':
              $node->style['text-decoration'] = 'line-through';
              break;
          }
          $node->setStyle($this->defaultInnerStyles);
          $this->setChunks($node->childNodes);
          break;
        case XML_TEXT_NODE:
          $this->Chunks[] = new Ximi_Chunk(
            $node->getNodeValue(),
            Ximi_Font::getFile(
              $node->parentNode->style['font-family'],
              $node->parentNode->style['font-style'],
              $node->parentNode->style['font-weight']
            ),
            str_replace('pt', '', $node->parentNode->style['font-size']),
            Ximi_Color::rgb($node->parentNode->style['color']),
            new $this->processors[$this->settings['processor']]()
          );
          break;
        default:
          break;
      }
    }
  }

  protected function setLines() {
    $lineCounter = 0;
    $chunkCount = count($this->Chunks);
    for ($i = 0; $i < $chunkCount; $i++) {
      // Create new line if not already created, trim the end of the last chunk
      if (!isset($this->Lines[$lineCounter])) {
        if ($lineCounter > 0) {
          $this->Chunks[$i-1]->trimEnd();
        }
        $this->Lines[$lineCounter] = new Ximi_Line();
        $this->Chunks[$i]->trimStart();
      }
      $this->Lines[$lineCounter]->Chunks[] = $this->Chunks[$i];
      if ($this->wordwrap
        && ($this->Lines[$lineCounter]->widthOfChunks()
          + $this->settings['padding-left']
          + $this->settings['padding-right']) > $this->settings['width']) {
        // If NOT multi word
        if (!$this->Chunks[$i]->isMultiWord()) {
          // If more than one chunk
          if (count($this->Lines[$lineCounter]->Chunks) > 1) {
            // Push the current chunk onto the new line
            array_pop($this->Lines[$lineCounter]->Chunks);
            $i--;
          }
          $lineCounter++;  // Increment line count for next chunk
          continue; // Continue to next chunk
        } else { // Split the chunk
          $this->insertChunk(clone $this->Chunks[$i], $i);
          $chunkCount++;
          $this->Chunks[$i]->text = '';
          $this->Chunks[$i]->setDimensions();
          while (($this->Lines[$lineCounter]->widthOfChunks()
            + $this->settings['padding-left']
            + $this->settings['padding-right']
            + $this->Chunks[$i+1]->firstWordWidth()) < $this->settings['width']) {
            $this->Chunks[$i]->text .= $this->Chunks[$i+1]->removeFirstWord();
            $this->Chunks[$i]->setDimensions();
          }
          $lineCounter++;  // Increment line count for next chunk
        }
      }
    }
  }

  protected function insertChunk($newChunk, $i) {
    if (isset($this->Chunks[$i+1])) {
      array_splice($this->Chunks, $i+1, 0, array($newChunk));
    } else {
      $this->Chunks[] = $newChunk;
    }
  }

  /**
   * this->maxLineWidth - width of longest line
   * this->totalLineHeight - total height of all lines
   * cursorY - y ordinate of top of a line
   * cursorX - x ordinate of left position of a chunk
   * line->y - y ordinate of a baseline of a line relative to top of canvas
   * line->width - width of the line (total width of all chunks)
   * line->height - height of the line (height of the tallest chunk)
   * chunk->x - x ordinate of the chunk relative to left of canvas
   */
  protected function processLines() {
    $cursorY = $this->settings['padding-top'];
    foreach ($this->Lines as $line) {
      $cursorX = $this->settings['padding-left'];
      foreach ($line->Chunks as $chunk) {
        $chunk->originX = $cursorX;
        $cursorX += $chunk->width;
        $line->finalOriginY = max($line->finalOriginY, $chunk->originY);
        $line->finalWidth += $chunk->width;
        $line->finalHeight = max($line->finalHeight, $chunk->height);
      }
      $leading = $line->finalHeight * $this->settings['line-height'] - $line->finalHeight;
      $line->finalOriginY += $cursorY + ($leading/2);
      $line->finalHeight += $leading;
      $cursorY += $line->finalHeight;
      $this->maxLineWidth = max($this->maxLineWidth, $line->finalWidth);
      $this->totalLineHeight += $line->finalHeight;
    }
    $this->maxLineWidth += $this->settings['padding-left'] + $this->settings['padding-right'];
    $this->totalLineHeight += $this->settings['padding-top'] + $this->settings['padding-bottom'];
  }

  protected function createCanvas() {
    if (!isset($this->settings['width']) || in_array($this->settings['clip'], array('width', 'both'))) {
      $this->settings['width'] = $this->maxLineWidth;
    }
    if (!isset($this->settings['height']) || in_array($this->settings['clip'], array('height', 'both'))) {
      $this->settings['height'] = $this->totalLineHeight;
    }
    $this->setYOffset();
    $this->Canvas = $this->ImageProcessor->createCanvas($this->settings['width'], $this->settings['height'], $this->settings['background-color']);
  }

  protected function addBackground() {
    if (!$this->settings['background-image']) {
      return;
    }
    $bg = $this->ImageProcessor->load($this->settings['background-image']);
    if (!$bg) {
      return;
    }
    list ($bgWidth, $bgHeight) = $this->ImageProcessor->dimensions($bg);
    if (!isset($this->settings['background-position'])
      || empty($this->settings['background-position'])) {
      $vertical = $horizontal = 0;
    } elseif (strpos($this->settings['background-position'], ' ') === false) {
      $vertical = $this->settings['background-position'];
      $horizontal = ($this->settings['width'] - $bgWidth) / 2;
    } else {
      list($vertical, $horizontal) = preg_split('/\s+/', $this->settings['background-position']);
    }
    if (in_array($vertical, array('top', 'center', 'bottom'))) {
      switch ($vertical) {
        case 'top':
          $bgY1 = 0;
          break;
        case 'center':
          $bgY1 = ($this->settings['height'] - $bgHeight) / 2;
          break;
        case 'bottom':
          $bgY1 = $this->settings['height'] - $bgHeight;
          break;
      }
    } elseif (preg_match('/^(\d+)%/', $vertical, $matches)) {
      $bgY1 = ($this->settings['height'] - $bgHeight) * $matches[1] / 100;
    } elseif (preg_match('/^(\d+)px/', $vertical, $matches)) {
      $bgY1 = $matches[1];
    } else {
      $bgY1 = 0;
    }
    /**
     * Horizontal
     */
    if (in_array($horizontal, array('left', 'center', 'right'))) {
      switch ($horizontal) {
        case 'left':
          $bgX1 = 0;
          break;
        case 'center':
          $bgX1 = ($this->settings['width'] - $bgWidth) / 2;
          break;
        case 'right':
          $bgX1 = $this->settings['width'] - $bgWidth;
          break;
      }
    } elseif (preg_match('/^(\d+)%/', $horizontal, $matches)) {
      $bgX1 = ($this->settings['width'] - $bgWidth) * $matches[1] / 100;
    } elseif (preg_match('/^(\d+)(px)?/', $horizontal, $matches)) {
      $bgX1 = $matches[1];
    } else {
      $bgX1 = 0;
    }
    if (!$this->settings['background-repeat']) {
      $this->settings['background-repeat'] = 'repeat';
    }
    switch ($this->settings['background-repeat']) {
      case 'no-repeat':
        $bgX2 = $bgX1 + $bgWidth;
        $bgY2 = $bgY1 + $bgHeight;
        break;
      case 'repeat-x':
        $bgX1 = 0;
        $bgX2 = $this->settings['width'];
        $bgY2 = $bgY1 + $bgHeight;
        break;
      case 'repeat-y':
        $bgY1 = 0;
        $bgX2 = $bgX1 + $bgWidth;
        $bgY2 = $this->settings['height'];
        break;
      case 'repeat':
      default:
        $bgX1 = $bgY1 = 0;
        $bgX2 = $this->settings['width'];
        $bgY2 = $this->settings['height'];
        break;
    }
    $bgW = $bgX2-$bgX1;
    $bgH = $bgY2-$bgY1;
    $this->ImageProcessor->addBackground($this->Canvas, $bg, $bgW, $bgH, $bgX1, $bgY1);
  }

  protected function setYOffset() {
    switch ($this->settings['vertical-align']) {
      case 'middle':
        $this->yOffset = ($this->settings['height'] - $this->totalLineHeight) / 2;
        break;
      case 'bottom':
        $this->yOffset = $this->settings['height'] - $this->totalLineHeight;
        break;
      case 'top':
      default:
        $this->yOffset = min(0, $this->settings['height'] - $this->totalLineHeight);
        break;
    }
  }

  protected function printChunks() {
    foreach ($this->Lines as $line) {
      $line->setFinalOriginX($this->settings['text-align'], $this->settings['width'] - $this->settings['padding-left'] - $this->settings['padding-right']);
      foreach ($line->Chunks as $chunk) {
        if (isset($this->settings['text-shadow'])) {
          foreach ($this->settings['text-shadow'] as $shadow) {
            if ($shadow['blur']) {
              $xBlurs = $yBlurs = range(-$shadow['blur'], $shadow['blur']);
            } else {
              $xBlurs = $yBlurs = array(0);
            }
            foreach ($yBlurs as $yBlur) {
              foreach ($xBlurs as $xBlur) {
                $opacity = 100 * pow(1.6, -1.8*(sqrt(pow($xBlur, 2) + pow($yBlur, 2))));
                if ($opacity <= 0) {
                  continue;
                }
                $x = $line->finalOriginX + $chunk->originX + $shadow['x'] + $xBlur;
                $y = $line->finalOriginY + $this->yOffset + $shadow['y'] + $yBlur;
                if ($this->settings['rotation']) {
                  list($x, $y) = $this->rotateCoord($x, $y);
                }
                $chunk->ImageProcessor->printText($this->Canvas, $chunk->size, $this->settings['rotation'], $x, $y, $shadow['color'], $chunk->font, $chunk->text, $opacity);
              }
            }
          }
        }
        $x = $line->finalOriginX + $chunk->originX;
        $y = $line->finalOriginY + $this->yOffset;
        if ($this->settings['rotation']) {
          list($x, $y) = $this->rotateCoord($x, $y);
        }
        $chunk->addToCanvas($this->Canvas, $x, $y, $this->settings['rotation']);
      }
    }
  }

  protected function rotateCoord($x, $y) {
    // Convert to new origin
    $x1 = $x - $this->settings['width']/2;
    $y1 = $this->settings['height'] - $y - $this->settings['height']/2;
    // Convert angle to anti-clockwise radians
    $theta = deg2rad(-$this->settings['rotation']);
    // Rotate
    $x = $x1 * cos($theta) + $y1 * sin($theta);
    $y = -$x1 * sin($theta) + $y1 * cos($theta);
    // Convert back to original origin
    $x = $x + $this->settings['width'] / 2;
    $y = $this->settings['height'] / 2 - $y;
    return array($x, $y);
  }

  /**
   * http://stackoverflow.com/questions/622140/calculate-bounding-box-coordinates-from-a-rotated-rectangle-picture-inside
   *
   */
  protected function rotate() {

    if (!$this->settings['rotation']) {
      return;
    }

    $radians = deg2rad($this->settings['rotation']);

    $oldVertices = array(
      'top left' => array(
        'x1' => 0,
        'y1' => $this->settings['height'],
      ),
      'top right' => array(
        'x1' => $this->settings['width'],
        'y1' => $this->settings['height'],
      ),
      'bottom right' => array(
        'x1' => $this->settings['width'],
        'y1' => 0,
      ),
      'bottom left' => array(
        'x1' => 0,
        'y1' => 0,
      ),
    );

    $newVertices = array();

    $x0 = floor($this->settings['width'] / 2);
    $y0 = floor($this->settings['height'] / 2);

    foreach ($oldVertices as $vertex => $coords) {
      $x1 = $coords['x1'];
      $y1 = $coords['y1'];
      $newVertices['x2'][] = $x0+($x1-$x0)*cos($radians)+($y1-$y0)*sin($radians);
      $newVertices['y2'][] = $y0-($x1-$x0)*sin($radians)+($y1-$y0)*cos($radians);
    }

    $widthAfterRotate = max($newVertices['x2']) - min($newVertices['x2']);
    $heightAfterRotate = max($newVertices['y2']) - min($newVertices['y2']);

    $widthScaled = floor(($this->settings['width'] / $widthAfterRotate) * $this->settings['width']);
    $heightScaled = floor(($this->settings['height'] / $heightAfterRotate) * $this->settings['height']);

    $this->Canvas = $this->ImageProcessor->resize($this->Canvas, $widthScaled, $heightScaled);
    $this->Canvas = $this->ImageProcessor->rotate($this->Canvas, $this->settings['rotation'], Ximi_Color::rgb($this->settings['background-color']));

  }

  protected function render() {
    header('Content-type: image/png');
    $this->ImageProcessor->render($this->Canvas);
    exit();
  }
}

function pr($var) {
  echo '<pre>';
  print_r($var);
  echo '</pre>';
}
function h($var) {
  return htmlentities($var);
}

$options = $_GET;
$html = $options['html'];
unset($options['html']);
new Ximi($html, $options);

?>