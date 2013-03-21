<?php
class Fieldtype_templates extends Fieldtype {

  function render() {
    $html = "<div class='input-select-wrap'><select name='{$this->fieldname}' tabindex='{$this->tabindex}'>";
    $html .= '<option value="">--Inherit--</option>';

    foreach (Statamic::get_templates_list() as $key) {
      $selected = $this->field_data == $key ? " selected='selected'" : '';     
      $html .= "<option {$selected} value='{$key}'>".ucwords($key)."</option>";
    }

    $html .= "</select></div>";

    return $html;
  }

}