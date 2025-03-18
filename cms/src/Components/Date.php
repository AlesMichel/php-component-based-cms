<?php

include_once("TextField.php");

class Date extends TextField
{
    public function getDataFieldsForEdit(): string
    {
        $out = '';
        if ($this->componentIsMultlang === 1) {
            $out .= "<label for='position_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . $this->componentName . " CZ</label>";
            $out .= "<input class='form-control' type='date' id='position_" . $this->componentName . "' value='" . htmlspecialchars($this->componentData) . "' name='component_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . " />";

            $out .= "<label for='position_" . $this->componentName . "_en' class='form-label mt-2 mb-1'>" . $this->componentName . " EN</label>";
            $out .= "<input class='form-control' type='date' id='position_" . $this->componentName . "_en' value='" . htmlspecialchars($this->componentDataEn) . "' name='component_en_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . " />";
        } else {
            $out = "<label for='position_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . $this->componentName . "</label>";
            $out .= "<input class='form-control' type='date' id='position_" . $this->componentName . "' value='" . htmlspecialchars($this->componentData) . "' name='component_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . " />";
        }
        return $out;
    }

    public function getDataFieldsForInsert(): string
    {
        $out = '';
        if ($this->componentIsMultlang === 1) {
            $out .= "<label for='position_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . $this->componentName . " CZ</label>";
            $out .= "<input class='form-control' type='date' id='position_" . $this->componentName . "' value='' name='component_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . " />";

            $out .= "<label for='position_" . $this->componentName . "_en' class='form-label mt-2 mb-1'>" . $this->componentName . " EN</label>";
            $out .= "<input class='form-control' type='date' id='position_" . $this->componentName . "_en' value='' name='component_en_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . " />";
        } else {
            $out .= "<label for='position_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . $this->componentName . "</label>";
            $out .= "<input class='form-control' type='date' id='position_" . $this->componentName . "' value='' name='component_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . " />";
        }
        return $out;
    }
}
