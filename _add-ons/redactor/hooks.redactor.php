<?php

class Hooks_redactor extends Hooks {

  function add_to_control_panel_head() {
    if (self::current_route() == 'publish') {
      return self::include_css(array('redactor.css', 'override.css'));
    }
  }

  function add_to_control_panel_foot() {
    if (self::current_route() == 'publish') {

      $html = self::include_js(array('fullscreen.js', 'redactor.min.js'));
      
      $options = Spyc::YAMLLoad('_config/add-ons/redactor.yaml');

      # Load image browser folder
      if (class_exists('Fieldtype_redactor') && method_exists('Fieldtype_redactor', 'get_field_settings')) {

        $field_settings = Fieldtype_redactor::get_field_settings();

        if (isset($field_settings['image_dir'])) {

          $path = Statamic_helper::reduce_double_slashes($field_settings['image_dir'].'/');

          // if (is_dir($path)) {
            $options['imageGetJson'] = Statamic::get_site_root()."TRIGGER/redactor/fetch_images?path={$path}";
            $options['imageUpload'] = Statamic::get_site_root()."TRIGGER/redactor/upload?path={$path}";
          // }
        }
      }

      $redactor_options = json_encode($options, JSON_FORCE_OBJECT);

      $html .= self::inline_js("
        
        var redactor_options = $redactor_options;
        
        $(document).ready(
          function() {
            $.extend(redactor_options, {'imageUploadErrorCallback': callback});
            $('.redactor-container textarea').redactor(redactor_options);
          }

        );

        $('body').on('addRow', '.grid', function() {
          $('.redactor-container textarea').redactor(redactor_options);
        });
        
        function callback(obj, json) {
          alert(json.error);
        }
      ");

      return $html;
    }
  }

  function upload() {

    if ( ! Statamic_Auth::get_current_user()) {
      exit("Invalid Request");
    }
    $app = \Slim\Slim::getInstance();
    $path = $app->request()->get('path');
    
    if (isset($path)) {
      $path = $path.'/';
      $dir = Statamic_helper::reduce_double_slashes(ltrim($path, '/').'/');
      if (is_dir($dir)) {


        $_FILES['file']['type'] = strtolower($_FILES['file']['type']);

        if ($_FILES['file']['type'] == 'image/png' 
        || $_FILES['file']['type'] == 'image/jpg' 
        || $_FILES['file']['type'] == 'image/gif' 
        || $_FILES['file']['type'] == 'image/jpeg') { 
        
          $file_info = pathinfo($_FILES['file']['name']);

          // pull out the filename bits
          $filename = $file_info['filename'];
          $ext = $file_info['extension'];

          // build filename
          $file = $dir.$filename.'.'.$ext;

          // check for dupes
          if (file_exists($file)) {
            $file = $dir.$filename.'-'.date('YmdHis').'.'.$ext;
          }

          if ( ! is_writable($dir)) {
            echo json_encode(array('error' => "Redactor: Unable to save file properly. Check the upload directory permissions."));
            die();
          }
         
          // copying
          copy($_FILES['file']['tmp_name'], $file);
            
            
          // display file
          $array = array(
            'filelink' => Statamic::get_site_root().$file
          );

          echo stripslashes(json_encode($array));
        }
      } else {
        echo json_encode(array('error' => "Redactor: Could not find directory: '$dir'"));
      }
    } else {
      echo json_encode(array('error' => "Redactor: Upload directory not set."));
    }

  }

  function fetch_images() {
    
    if ( ! Statamic_Auth::get_current_user()) {
      exit("Invalid Request");
    }

    $app = \Slim\Slim::getInstance();

    $path = $app->request()->get('path');
    $dir = Statamic_helper::reduce_double_slashes(ltrim($path, '/').'/');

    $image_list = glob($dir."*.{jpg,jpeg,gif,png}", GLOB_BRACE);
    $images = array();

    if (count($image_list) > 0) {
      foreach ($image_list as $image) {
        $images[] = array(
          'thumb' => Statamic::get_site_root().$image,
          'image' => Statamic::get_site_root().$image
        );
      }
    }


    echo json_encode($images);
  }

}