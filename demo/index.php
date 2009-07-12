<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Ximi</title>
    <link type="text/css" href="reset.css" />
    <link rel="stylesheet" href="ui.colorpicker.css" />
    <style type="text/css">
    body {
      font: 12px arial;
      background-image:url(lgrey177.gif);
    }
    #page {
      width: 960px;
    }
    fieldset {
      width: 190px;
      float: left;
      padding: 0;
      border: none;
      margin: 0;
    }
    #controls div {
      margin: 5px 0;
    }
    .text label {
      display: block;
      float: left;
      width: 60px;
    }
    .text input {
      margin-right: 3px;
    }
    .radio ul {
      margin: 0;
      padding: 0;
      list-style: none;
    }
    .items3 li {
      width: 60px;
      float: left;
    }
    .items4 li {
      width: 90px;
      float: left;
    }
    p {
      margin: 0;
      padding: 0;
    }
    #code {
      width: 470px;
      height: 100px;
      float: left;
    }
    #code textarea {
      width: 460px;
      height: 80px;
    }
    #src {
      width: 470px;
      height: 100px;
      float: left;
    }
    #html,#gd,#im {
      width: 310px;
      height: 310px;
      float: left;
      overflow: auto;
      border: 1px solid red;
    }
    #html {
    }
    #gd img,#im img {
      display: block;
    }
    .clearfix:after {
      content: ".";
      display: block;
      height: 0;
      clear: both;
      visibility: hidden;
    }
    </style>
    <!--[if IE]>
    <style type="text/css">
      .clearfix {
        zoom: 1;     /* triggers hasLayout */
        }  /* Only IE can see inside the conditional comment
        and read this CSS rule. Don't ever use a normal HTML
        comment inside the CC or it will close prematurely. */
    </style>
    <![endif]-->
  </head>
  <body>
    <div id="page">
      <form action="" method="post">
        <div id="controls" class="clearfix">
          <?php
  //    'processor' => 'GD',
  //    'angle' => 0,
  //    'mime_type' => 'image/png',
          function label($for, $label) {
            return '<label for="'.$for.'">'.$label.'</label>';
          }
          $controls = array(
            'Dimensions' => array(
              'width' => array(
                'size' => '3',
                'default' => '300',
                'after' => 'px',
              ),
              'height' => array(
                'size' => '3',
                'default' => '300',
                'after' => 'px',
              ),
              'clip' => array(
                'type' => 'radio',
                'options' => array(
                  'none' => 'none',
                  'both' => 'both',
                  'width' => 'width',
                  'height' => 'height',
                ),
                'default' => 'none',
                'between' => '<abbr style="clear:both;" title="Defines which dimensions of the Ximi should be clipped to the text. Overrides dimensions above.">?</abbr>',
              ),
            ),
            'Alignment' => array(
              'vertical-align' => array(
                'type' => 'radio',
                'options' => array(
                  'top' => 'top',
                  'middle' => 'middle',
                  'bottom' => 'bottom',
                ),
                'default' => 'middle',
              ),
              'text-align' => array(
                'type' => 'radio',
                'options' => array(
                  'left' => 'left',
                  'center' => 'center',
                  'right' => 'right',
                ),
                'default' => 'center',
              ),
              'text-shadow' => array(
                'label' => 'Shadow',
                'before' => '#',
                'default' => 'eeeeee 5px 5px 1px',
                'after' => '<br />color x y blur',
              ),
            ),
            'Padding' => array(
              'padding-top' => array(
                'label' => 'Top',
                'size' => '3',
                'default' => '10',
                'after' => 'px',
              ),
              'padding-right' => array(
                'label' => 'Right',
                'size' => '3',
                'default' => '10',
                'after' => 'px',
              ),
              'padding-bottom' => array(
                'label' => 'Bottom',
                'size' => '3',
                'default' => '10',
                'after' => 'px',
              ),
              'padding-left' => array(
                'label' => 'Left',
                'size' => '3',
                'default' => '10',
                'after' => 'px',
              ),
            ),
            'Background' => array(
              'background-color' => array(
                'label' => 'Color',
                'size' => '6',
                'default' => 'transparent',
              ),
              'background-image' => array(
                'label' => 'Image',
                'size' => '6',
                'default' => 'http://uk.php.net/images/php.gif',
                'after' => '<abbr style="clear:both;" title="Allowed values are &quot;none&quot; or an absolute URL to background image, e.g. http://'.$_SERVER['HTTP_HOST'].'/bg.png">?</abbr>',
              ),
              'background-position' => array(
                'label' => 'Position',
                'default' => '0 0',
                'size' => '6',
              ),
              'background-repeat' => array(
                'label' => 'Repeat',
                'type' => 'radio',
                'options' => array(
                  'repeat' => 'repeat',
                  'repeat-x' => 'repeat-x',
                  'no-repeat' => 'no-repeat',
                  'repeat-y' => 'repeat-y',
                ),
                'default' => 'repeat',
              ),
              'background-attachment' => array(
                'label' => 'Attachment',
                'type' => 'radio',
                'options' => array(
                  'scroll' => 'scroll',
                  'fixed' => 'fixed',
                ),
                'default' => 'scroll',
              ),
            ),
            'Misc' => array(
              'color' => array(
                'between' => '#',
                'size' => '6',
                'default' => '000',
              ),
              'line-height' => array(
                'size' => '3',
                'default' => '1.2',
                'label' => '<span style="white-space:nowrap">Line-height</span>',
              ),
              'rotation' => array(
                'size' => '3',
                'default' => '0',
                'after' => 'deg <abbr style="clear:both;" title="Allowed values are 0 to 360">?</abbr>',
              ),
              'format' => array(
                'type' => 'radio',
                'options' => array(
                  'png' => 'png',
                  'jpg' => 'jpg',
                  'gif' => 'gif',
                ),
                'default' => 'png',
              ),
            ),
          );
          foreach ($controls as $legend => $fields) {
            echo '<fieldset><legend>'.$legend.'</legend>';
            foreach ($fields as $name => $settings) {
              extract($settings, EXTR_OVERWRITE);
              if (!isset($settings['type'])
              || ($settings['type'] == 'radio' && (!isset($settings['options']) || empty($settings['options'])))) {
                $type = 'text';
              }
              if (!isset($settings['label'])) {
                $label = strtoupper(substr($name, 0, 1)).substr($name, 1);
              }
              if (!isset($settings['class'])) {
                $class = $type;
              }
              $id = $name;
              extract($settings, EXTR_OVERWRITE);
              echo '<div class="'.$class.'">';
              if (isset($settings['before'])) {
                echo $settings['before'];
              }
              switch ($type) {
                case 'text':
                  echo label($id, $label);
                  if (isset($settings['between'])) {
                    echo $settings['between'];
                  }
                  $extra = '';
                  if (isset($settings['default'])) {
                    $extra .= ' value="'.$settings['default'].'"';
                  }
                  if (isset($settings['size'])) {
                    $extra .= ' size="'.$settings['size'].'"';
                  }
                  echo '<input type="text" id="'.$id.'" name="'.$name.'"'.$extra.' />';
                  break;
                case 'radio':
                  echo '<p>'.$label.'</p>';
                  echo '<ul class="items'.count($settings['options']).'">';
                  foreach ($settings['options'] as $value => $text) {
                    $id = $name . '-' . $value;
                    $checked = '';
                    if (isset($settings['default']) && $settings['default'] == $value) {
                      $checked = ' checked="checked"';
                    }
                    echo '<li>';
                    echo '<input type="radio" id="'.$id.'" name="'.$name.'" value="'.$value.'"'.$checked.' />';
                    echo label($id, $text);
                    echo '</li>';
                  }
                  echo '</ul>';
                  break;
                case 'select':
                  echo '<p>'.$label.'</p>';
                  echo '<select name="'.$name.'">';
                  foreach ($settings['options'] as $value => $text) {
                    $selected = '';
                    if (isset($settings['default']) && $settings['default'] == $value) {
                      $selected = ' selected="selected"';
                    }
                    echo '<option value="'.$value.'"'.$checked.'>'.$text.'</option>';
                  }
                  echo '</select>';
                  break;

                default:
                  break;
              }
              if (isset($settings['after'])) {
                echo $settings['after'];
              }
              echo '</div>';
            }
            echo '</fieldset>';
          }
          ?>
        </div>
        <div class="clearfix">
          <div id="code">
            <textarea name="html"><span style="font-size:30pt;">The <em style="font-size:20pt;">quick</em> <span style="color:brown">brown</span> fox jumps over the lazy dog</span></textarea>
          </div>
          <div id="src">

          </div>
        </div>
      </form>
      <div class="clearfix" style="margin-top:20px">
        <div id="html">

        </div>
        <div id="gd">
          <img src="" />
        </div>
        <div id="im">
          <img src="" />
        </div>
      </div>
    </div>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        function Ximi() {
          this.set = function (property, value) {
            this[property] = value;
          };
          this.render = function () {
            var src = '../ximi.php?';
            for (prop in this) {
              if (this[prop].call) {
                continue;
              }
              src = src+prop+'='+this[prop]+'&';
            }
            $("#src").text(src);
            $("#html").html($("#code textarea").val());
            $("#gd img").attr('src', src+'processor=GD');
            $("#im img").attr('src', src+'processor=IM');
          };
        }
        var Ximi = new Ximi();
        $("form").bind("submit", function(event){
          event.preventDefault();
        });
        $("form input, form select, form textarea").each(function () {
          if (this.nodeName == 'textarea') {
            $(this).bind("blur", function(e){
              Ximi.set(e.target.name, e.target.value);
              Ximi.render();
            });
          } else {
            $(this).bind("change", function(e){
              Ximi.set(e.target.name, e.target.value);
              Ximi.render();
            });
          }
          if (this.type == 'radio' && this.checked == false) {
            return true;
          }
          Ximi.set(this.name, this.value);
        });
        Ximi.render();
      });
    </script>
  </body>
</html>

