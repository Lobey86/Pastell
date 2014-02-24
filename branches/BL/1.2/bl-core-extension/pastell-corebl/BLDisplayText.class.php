<?php

require_once(__DIR__ . "/BLDisplayValue.class.php");

class BLDisplayText extends BLDisplayValue {

    const INDENT = '- ';

    protected function encode($text) {
        $result = utf8_encode($text);
        return $result;
    }

    protected function formatCellHeader($colIndex, $colName) {
        return null;
    }

    protected function formatRowHeader($rowValue) {
        return null;
    }

    protected function formatCell($rowIndex, $colIndex, $colName, $cellValue) {
        $result = '';
        if ($colIndex) {
            $result .= '; ';
        }
        if ($colName) {
            $result .= $colName . '=';
        }
        $result .= $cellValue;
        return $result;
    }

    protected function formatRow($rowIndex, $rowValue) {
        $rowValue = str_replace("\n", "\n" . str_repeat(' ', strlen(self::INDENT)), $rowValue);
        $result = self::INDENT . $rowValue;
        if ($rowIndex) {
            $result = "\n" . $result;
        }
        return $result;
    }

    protected function formatTable($tableValue) {
        return $tableValue;
    }

    protected function concat(&$target, &$source) {
        if ($target) {
            $target .= "\n";
        }
        $target .= $source;
        return $target;
    }

    protected function arrayDisplay(array $array, $doEncoding = true) {
        if (count($array) == 0) {
            $tableDisplay = $this->stringDisplay('aucun élément', $doEncoding);
        } else {
            // Les titres des colonnes sont affichées si le tableau est multi-colonnes;
            // c'est le cas si les lignes elles-mêmes sont des tableaux; les titres des colonnes
            // sont alors les keys de la première ligne.
            // Toutes les lignes sont censées avoir la même structure; le comportement n'est pas
            // déterminé dans le cas contraire.
            $this->hasHeader = false;
            $tableValue = '';
            $firstElem = $array[0];
            if (is_array($firstElem)) {
                $columns = array_keys($firstElem);
                if (is_string($columns[0])) {
                    $this->hasHeader = true;
                    $rowValue = '';
                    $colIndex = 0;
                    foreach ($columns as $colName) {
                        $colNameValue = $this->stringDisplay($colName, $doEncoding);
                        $cellDisplay = $this->formatCellHeader($colIndex, $colNameValue);
                        if ($cellDisplay) {
                            $rowValue .= $cellDisplay;
                        }
                        $colIndex++;
                    }
                    $rowDisplay = $this->formatRowHeader($rowValue);
                    if ($rowDisplay) {
                        $tableValue .= $rowDisplay;
                    }
                }
            }
            $rowIndex = 0;
            foreach ($array as $tr) {
                $colIndex = 0;
                if (is_array($tr)) {
                    $rowValue = '';
                    $columns = array_keys($tr);
                    foreach ($columns as $colName) {
                        $cellValue = $tr[$colName];
                        $datetime = strtotime($cellValue);
                        if ($datetime !== false) {
                            $cellValue = date("d/m/Y H:i:s", $datetime);
                        }
                        $colNameValue = $this->stringDisplay($colName, $doEncoding);
                        $cellValue = $this->stringDisplay($cellValue, $doEncoding);
                        $cellDisplay = $this->formatCell($rowIndex, $colIndex, $colNameValue, $cellValue);
                        $rowValue .= $cellDisplay;
                        $colIndex++;
                    }
                } else {
                    $cellValue = $this->stringDisplay($tr, $doEncoding);
                    $rowValue = $this->formatCell($rowIndex, $colIndex, null, $cellValue);
                }
                $rowDisplay = $this->formatRow($rowIndex, $rowValue);
                $tableValue .= $rowDisplay;
                $rowIndex++;
            }
            $tableDisplay = $this->formatTable($tableValue);
        }
        return $tableDisplay;
    }

}
