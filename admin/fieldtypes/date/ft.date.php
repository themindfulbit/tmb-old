<?php
class Fieldtype_date extends Fieldtype {

  function render() {
    $html = "<input type='text' name='{$this->fieldname}' tabindex='{$this->tabindex}' value='{$this->field_data}' />";
    return $html;
  }

}