<?php

include_once("component.php");
class SelectBox extends component
{
    public function getDataFieldsForEdit(): string
    {
        $options = self::getOptions($this->moduleId, $this->componentName, $this->db);
        $out = '';

        // Generování select boxu pro vícejazyčnou podporu
        if ($this->componentIsMultlang === 1) {
            // Česká verze
            $out .= "<label for='select_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . htmlspecialchars($this->componentName) . " CZ</label>";
            $out .= "<select class='form-select mb-3' id='select_" . $this->componentName . "' name='component_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . ">";
            foreach ($options as $option) {
                $out .= "<option value='" . htmlspecialchars($option['id']) . "'>" . htmlspecialchars($option['value']) . "</option>";
            }
            $out .= "</select>";

            // Anglická verze
            $out .= "<label for='select_" . $this->componentName . "_en' class='form-label mt-2 mb-1'>" . htmlspecialchars($this->componentName) . " EN</label>";
            $out .= "<select class='form-select' id='select_" . $this->componentName . "_en' name='component_en_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . ">";
            foreach ($options as $option) {
                $out .= "<option value='" . htmlspecialchars($option['id']) . "'>" . htmlspecialchars($option['valueEn']) . "</option>";
            }
            $out .= "</select>";
        } else {
            // Jednojazyčná verze
            $out .= "<label for='select_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . htmlspecialchars($this->componentName) . "</label>";
            $out .= "<select class='form-select mb-3' id='select_" . $this->componentName . "' name='component_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . ">";
            foreach ($options as $option) {
                $out .= "<option value='" . htmlspecialchars($option['id']) . "'>" . htmlspecialchars($option['value']) . "</option>";
            }
            $out .= "</select>";
        }

        return $out;
    }

    public function getDataFieldsForInsert(): string
    {
        $options = self::getOptions($this->moduleId, $this->componentName, $this->db);
        $out = '';

        // Generování select boxu pro vícejazyčnou podporu
        if ($this->componentIsMultlang === 1) {
            // Česká verze
            $out .= "<label for='select_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . htmlspecialchars($this->componentName) . " CZ</label>";
            $out .= "<select class='form-select mb-3' id='select_" . $this->componentName . "' name='component_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . ">";
            foreach ($options as $option) {
                $out .= "<option value='" . htmlspecialchars($option['id']) . "'>" . htmlspecialchars($option['value']) . "</option>";
            }
            $out .= "</select>";

            // Anglická verze
            $out .= "<label for='select_" . $this->componentName . "_en' class='form-label mt-2 mb-1'>" . htmlspecialchars($this->componentName) . " EN</label>";
            $out .= "<select class='form-select' id='select_" . $this->componentName . "_en' name='component_en_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . ">";
            foreach ($options as $option) {
                $out .= "<option value='" . htmlspecialchars($option['id']) . "'>" . htmlspecialchars($option['valueEn']) . "</option>";
            }
            $out .= "</select>";
        } else {
            // Jednojazyčná verze
            $out .= "<label for='select_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . htmlspecialchars($this->componentName) . "</label>";
            $out .= "<select class='form-select mb-3' id='select_" . $this->componentName . "' name='component_" . $this->componentName . "' " . ($this->componentIsRequired ? 'required' : '') . ">";
            foreach ($options as $option) {
                $out .= "<option value='" . htmlspecialchars($option['id']) . "'>" . htmlspecialchars($option['value']) . "</option>";
            }
            $out .= "</select>";
        }

        return $out;
    }


    /**
     * @param $moduleId
     * @param $db
     * get all options for select box in current module
     * @return array
     */
    public static function getOptions($moduleId, $componentName, $db): array
    {
        try {
            $sql = "SELECT * FROM component_resource WHERE module_id = :moduleId AND component_name = :componentName";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                "moduleId" => $moduleId,
                "componentName" => $componentName
            ]);

            // Vrací výsledek jako pole
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // V případě chyby vypíšeme chybovou zprávu
            echo $e->getMessage();
            return [];  // Pokud dojde k chybě, vrátí prázdné pole
        }
    }

    public function getDataFormated(): bool|array
    {
        return $this->getValueForId($this->componentData);
    }

    private function getValueForId($id)
    {
        try {
            $sql = "SELECT value, valueEn FROM component_resource WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(["id" => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Pokud výsledek existuje, zkontroluj valueEn a vrátí pole [value, valueEn]
            if ($result) {
                // Pokud valueEn je prázdné, nastavíme hodnotu null
                if (empty($result['valueEn'])) {
                    return [$result['value'], null];
                } else {
                    return [$result['value'], $result['valueEn']];
                }
            } else {
                // Pokud nic nenalezeno, vrátí [null, null]
                return [null, null];
            }
        } catch (Exception $e) {
            // V případě chyby vypíšeme chybovou zprávu
            echo $e->getMessage();
            return false;
        }
    }

}
