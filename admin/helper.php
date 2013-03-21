<?php
class Cp_Helper {

  public static function run_hook($hook_name) {

    # check the add-on directory
    $output = '';

    $locations = array(Statamic::get_admin_path().'fieldtypes/*', '_add-ons/*');

    foreach ($locations as $folder) {

      $list = glob($folder);

        if ($list) {
        foreach ($list as $name) {
          if (is_dir($name)) {
            $add_on_files = glob($name.'/*');
            foreach ($add_on_files as $file) {
              if (preg_match('/hooks/', $file, $matches )) {


                $bits = explode('/', $name);
                $add_on = end($bits);

                #instantiate
                require_once($file);
                $class = 'Hooks_'.$add_on;

                #formatted properly
                if (class_exists($class)) {
                  $add_on_hooks = new $class();
                }

                # get output
                if (method_exists($add_on_hooks, $hook_name)) {
                  $output .= $add_on_hooks->$hook_name();
                  $output .= "\n";
                }
              }
            }
          }
        }
      }
    }
    return $output;
  }

  public static function show_page($page, $default = true) {
    $app = \Slim\Slim::getInstance();

    if (isset($app->config['_admin_nav'][$page])) {
      return $app->config['_admin_nav'][$page];
    }
    
    return $default;
  }

  public static function nav_count() {
    $app = \Slim\Slim::getInstance();

    $count = 0;
    if (isset($app->config['_admin_nav']) && count($app->config['_admin_nav'] == 7)) {
      foreach ($app->config['_admin_nav'] as $nav) {
        if ($nav === TRUE) {
          $count ++;
        }
      }
      return (string)$count;
    }

    return '7';

  }
}
