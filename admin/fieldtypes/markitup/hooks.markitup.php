<?php
class Hooks_markitup extends Hooks {

  function add_to_control_panel_head() {
    if (self::current_route() == 'publish') {
      return self::include_css('markitup.css', Statamic::get_admin_path().'fieldtypes/');
    }
  }

  function add_to_control_panel_foot() {
    if (self::current_route() == 'publish') {
      $path = Statamic::get_admin_path().'fieldtypes/';
      
      $html = self::include_js('jquery.markitup.js', $path);
      $html .= self::include_js('set.js', $path);

      $html .= self::inline_js("
        $(function() {
          $('.input-markitup textarea').markItUp(mySettings);
        });
      ");

      return $html;
    }

  }

}