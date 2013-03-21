<?php
class Fieldtype_checkbox extends Fieldtype {
	function render_field() {
		$html  = "<div class='checkbox-block'>";
		$html .= $this->render();
		$html .= $this->render_label();
		$html .= $this->render_instructions();
		$html .= "</div>";

		return $html;
	}

	function render() {
		$checked = ($this->field_data) ? ' checked="checked"' : '';

		return "<input type='checkbox' name='{$this->fieldname}' tabindex='{$this->tabindex}' class='checkbox' id='{$this->field_id}' value='1'{$checked} />";
	}
}
?>