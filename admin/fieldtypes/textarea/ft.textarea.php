<?php
class Fieldtype_textarea extends Fieldtype {

  function render() {
    $height = isset($this->field_config['height']) ? $this->field_config['height'].'px' : '150px';
    $html = "<textarea name='{$this->fieldname}' tabindex='{$this->tabindex}' style='height: {$height}'>{$this->field_data}</textarea>";
    return $html;
  }

}