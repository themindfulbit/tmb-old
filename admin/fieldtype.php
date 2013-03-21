<?php
class Fieldtype {
  /**
   * field_id
   * ID to use in the HTML of the field itself
   */
  public $field_id = null;


  /**
   * init
   * Allows field setup, called before rendering happens
   */
  protected function init() {
    $this->field_id = Statamic_helper::random_string();
  }


  /**
   * render_label
   * Renders the HTML label for this field
   */
  public function render_label() {
    if (!isset($this->field_config['display']) || !$this->field_config['display']) {
      return "";
    }

    // if a field ID was given, use that as the for attribute connector
    $for = (!is_null($this->field_id)) ? ' for="' . $this->field_id . '"' : '';

    return "<label{$for}>{$this->field_config['display']}</label>";
  }


  /**
   * render_instructions
   * Renders the HTML for this field's instructions
   */
  public function render_instructions() {
    if (!isset($this->field_config['instructions']) || !$this->field_config['instructions']) {
      return "";
    }

    return "<small>{$this->field_config['instructions']}</small>";
  }


  /**
   * render_field
   * Renders the full HTML for this field
   */
  public function render_field() {
    return $this->render_label() . $this->render() . $this->render_instructions();
  }


  public static function render_fieldtype($fieldtype, $fieldname, $field_config, $field_data, $tabindex=null, $input_key='[yaml]') {

    $output = '';

    $fieldtype_folders = array('_add-ons/', Statamic::get_admin_path().'fieldtypes/');
    
    foreach ($fieldtype_folders as $folder) {
      if (is_dir($folder.$fieldtype) && is_file($folder.$fieldtype.'/ft.'.$fieldtype.'.php')) {
      
        $file = $folder.$fieldtype.'/ft.'.$fieldtype.'.php';
        break;
      
      } elseif (is_file($folder.'/ft.'.$fieldtype.'.php')) {
      
        $file = $folder.'/ft.'.$fieldtype.'.php';
        break;
      }
    }

    # fieldtype exists
    if (isset($file)) {

      require_once($file);
      $class = 'Fieldtype_'.$fieldtype;

      #formatted properly
      if (class_exists($class)) {
        $field = new $class();
      }

      # function exists
      if (method_exists($field, 'render')) {
        $field->field_config    = $field_config;
        $field->field           = "$fieldname";
        $field->fieldname       = "page{$input_key}[$fieldname]";
        $field->fieldnameremove = "page{$input_key}[{$fieldname}_remove]";
        $field->field_data      = $field_data;
        $field->tabindex        = $tabindex;

        if (method_exists($field, 'init')) {
          $field->init();
        }

        $output = $field->render_field();
      }
        
    }
    
    return $output;
  }

  public static function process_field_data($fieldtype, $field_data, $settings=null) {

    $fieldtype_folders = array('_add-ons/', Statamic::get_admin_path().'fieldtypes/');
    
    foreach ($fieldtype_folders as $folder) {
      if (is_dir($folder.$fieldtype) && is_file($folder.$fieldtype.'/ft.'.$fieldtype.'.php')) {
      
        $file = $folder.$fieldtype.'/ft.'.$fieldtype.'.php';
        break;
      
      } elseif (is_file($folder.'/ft.'.$fieldtype.'.php')) {
      
        $file = $folder.'/ft.'.$fieldtype.'.php';
        break;
      }
    }

    # fieldtype exists
    if (isset($file)) {

      require_once($file);
      $class = 'Fieldtype_'.$fieldtype;

      #formatted properly
      if (class_exists($class)) {
        $field = new $class();
      }

      # function exists
      if (method_exists($field, 'process')) {
        $field->field_data = $field_data;
        $field->settings   = $settings;
        $field_data        = $field->process();
      }
        
    }

    return $field_data;
  }

}