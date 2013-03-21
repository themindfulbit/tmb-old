<?php
class Fieldtype_file extends Fieldtype {

  function render() {
    $html = '';
    if ($this->field_data) {

      $html .= "<p>Current file: ".basename($this->field_data)."</p>";
      $html .= "<p>Remove current file? <input type='checkbox' name='{$this->fieldnameremove}' value='1' /></p>";
      // $html .= "<div class='file-field-container'>";
      //   $html .= "<a class='file-field-remove icon'>[</a>";
      //   $html .= "<img src='{$this->field_data}'>";
      // $html .= "</div>";
      // $html .= "<input type='hidden' name='{$this->fieldnameremove}' value='0' />";
    }

    $html .= "<input type='file' name='{$this->fieldname}' tabindex='{$this->tabindex}' value='' />";
    $html .= "<input type='hidden' name='{$this->fieldname}' value='{$this->field_data}' />";
    return $html;
  }

  function process() {
    $processed_data = '';
    if ($this->field_data['tmp_name'] <> '') {
      if ( ! file_exists($this->settings['destination'])) {  
        mkdir($this->settings['destination'], 0777, true);
      }

      if (move_uploaded_file($this->field_data['tmp_name'], $this->settings['destination']."/".$this->field_data['name'])) {
        $processed_data = Statamic_helper::reduce_double_slashes("/".$this->settings['destination']."/".$this->field_data['name']);
        // path to the uploaded file
      } else {
        die ("File upload failed");
      }
    }

    return $processed_data;
  }

}