<?php

class Ximi_Font {
  static $fontFiles = array(
    'arial' => array(
      'normal' => array('normal' => 'arial.ttf','bold' => 'arialbd.ttf'),
      'italic' => array('normal' => 'ariali.ttf','bold' => 'arialbi.ttf')
    ),
    'sans-serif' => array(
      'normal' => array('normal' => 'arial.ttf','bold' => 'arialbd.ttf'),
      'italic' => array('normal' => 'ariali.ttf','bold' => 'arialbi.ttf')
    ),
    'arial narrow' => array(
      'normal' => array('normal' => 'arialn.ttf','bold' => 'arialnb.ttf'),
      'italic' => array('normal' => 'arialni.ttf','bold' => 'arialnbi.ttf')
    ),
    'verdana' => array(
      'normal' => array('normal' => 'verdana.ttf','bold' => 'verdanab.ttf'),
      'italic' => array('normal' => 'verdanai.ttf','bold' => 'verdanaz.ttf')
    ),
    'trebuc' => array(
      'normal' => array('normal' => 'trebuc.ttf','bold' => 'trebucbd.ttf'),
      'italic' => array('normal' => 'trebucit.ttf','bold' => 'trebucbi.ttf')
    ),
    'times' => array(
      'normal' => array('normal' => 'times.ttf','bold' => 'timesbd.ttf'),
      'italic' => array('normal' => 'timesi.ttf','bold' => 'timesbi.ttf')
    ),
    'serif' => array(
      'normal' => array('normal' => 'times.ttf','bold' => 'timesbd.ttf'),
      'italic' => array('normal' => 'timesi.ttf','bold' => 'timesbi.ttf')
    ),
    'georgia' => array(
      'normal' => array('normal' => 'georgia.ttf','bold' => 'georgiab.ttf'),
      'italic' => array('normal' => 'georgiai.ttf','bold' => 'georgiaz.ttf')
    ),
    'garamond' => array(
      'normal' => array('normal' => 'gara.ttf','bold' => 'garabd.ttf'),
      'italic' => array('normal' => 'garait.ttf','bold' => '')
    ),
    'courier' => array(
      'normal' => array('normal' => 'cour.ttf','bold' => 'courb.ttf'),
      'italic' => array('normal' => 'couri.ttf','bold' => 'courbi.ttf')
    ),
    'century gothic' => array(
      'normal' => array('normal' => 'gothic.ttf','bold' => 'gothicb.ttf'),
      'italic' => array('normal' => 'gothici.ttf','bold' => 'gothicbi.ttf')
    ),
    'bookman old style' => array(
      'normal' => array('normal' => 'bookos.ttf','bold' => 'bookosb.ttf'),
      'italic' => array('normal' => 'bookosi.ttf','bold' => 'bookosbi.ttf')
    ),
    'book antiqua' => array(
      'normal' => array('normal' => 'bkant.ttf','bold' => 'antquab.ttf'),
      'italic' => array('normal' => 'antquai.ttf','bold' => 'antquabi.ttf')
    ),
    'folio' => array(
      'normal' => array('normal' => 'FolioBTBolCon.ttf','bold' => 'FolioBTBolCon.ttf'),
      'italic' => array('normal' => 'FolioBTBolCon.ttf','bold' => 'FolioBTBolCon.ttf')
    )
  );
  public static function getFile($family, $style, $weight) {

    $font = 'arial'; //@todo Get default from HTMLDOMElement
    $families = preg_split('/[,\s"\']+/', $family);
    foreach ($families as $family) {
      if (array_key_exists($family, self::$fontFiles)) {
        $font = $family;
        break;
      }
    }

    if (isset(self::$fontFiles[$font][$style][$weight])) {
      $file = self::$fontFiles[$font][$style][$weight];
    } elseif (isset(self::$fontFiles[$font]['normal'][$weight])) {
      $file = self::$fontFiles[$font]['normal'][$weight];
    } elseif (isset(self::$fontFiles[$font][$style]['normal'])) {
      $file = self::$fontFiles[$font][$style]['normal'];
    } elseif (isset($fontFiles[$font]['normal']['normal'])) {
      $file = self::$fontFiles[$font]['normal']['normal'];
    }

    return 'fonts/'.$file;
  }
}

?>
