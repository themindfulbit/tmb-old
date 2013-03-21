<?php

class Hooks {

  public static function current_route() {
    $app = \Slim\Slim::getInstance();

    return trim($app->request()->getResourceUri(), '/');
  }

  public static function include_js($file, $root = '_add-ons/') {

    $class = get_called_class();
    $add_on = substr($class, 6);

    if ( ! is_array($file)) {
      $files[] = $file;
    } else {
      $files = $file;
    }

    $html = '';
    foreach ($files as $key => $file) {
      $file = Statamic::get_site_root().$root.$add_on.'/js/'.$file;

      if (substr($file, -3) != '.js') {
        $file .= '.js';
      }

      $html .= "<script type=\"text/javascript\" src=\"{$file}\"></script>\n";
    }

    return $html;
  }

  public static function include_css($file, $root = '_add-ons/') {
    $class = get_called_class();
    $add_on = substr($class, 6);

    if ( ! is_array($file)) {
      $files[] = $file;
    } else {
      $files = $file;
    }

    $html = '';
    foreach ($files as $key => $file) {
      $file = Statamic::get_site_root().$root.$add_on.'/css/'.$file;

      if (substr($file, -4) != '.css') {
        $file .= '.css';
      }

      $html .= "<link rel=\"stylesheet\" href=\"{$file}\">\n";
    }

    return $html;
  }

  public static function inline_js($html) {
    return "<script type=\"text/javascript\">\n$html\n</script>\n";
  }
}