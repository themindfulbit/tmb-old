<?php
class Fieldtype_tags extends Fieldtype {

  function render() {

    if (is_array($this->field_data)) {
      $this->field_data = implode(", ", $this->field_data);
    }

    $html = "<input type='text' name='{$this->fieldname}' tabindex='{$this->tabindex}' value='{$this->field_data}' />";
    return $html;
  }

  function process() {
      $processed_data = '';
    if ($this->field_data <> '') {
      $processed_data = explode(',', $this->field_data);
    }
    return $processed_data;
  }

}