<?php

require_once(__DIR__ . "/BLDisplayValue.class.php");

class BLDisplayHtml extends BLDisplayValue {

    protected function encode($text) {
        $result = htmlentities($text);
        return $result;
    }

    protected function concat(&$target, &$source) {
        if ($target) {
            $target .= '<br>';
        }
        $target .= $source;
        return $target;
    }

    protected function formatCellHeader($colIndex, $colName) {
        $result = '<th>' . $colName . '</th>';
        return $result;
    }

    protected function formatRowHeader($rowValue) {
        return $this->formatRow(0, $rowValue);
    }

    protected function formatCell($rowIndex, $colIndex, $colName, $cellValue) {
        $result = '<td>' . $cellValue . '</td>';
        return $result;
    }

    protected function formatRow($rowIndex, $rowValue) {
        $result = '<tr>' . $rowValue . '</tr>';
        return $result;
    }

    protected function formatTable($headerValue, $bodyValue) {
        $hasHeader = !empty($headerValue);
        $hasBody = !empty($bodyValue);
        $result = '';
        if ($hasHeader || $hasBody) {
            $result = '<table border=1 cellspacing=0>';
        }
        if ($hasHeader) {
            $result .= '<thead align="left">' . $headerValue . '</thead>';
        }
        if ($hasBody) {
            $result .= '<tbody valign="top">' . $bodyValue . '</tbody>';
        }
        if ($hasHeader || $hasBody) {
            $result .= '</table>';
        }
        return $result;
    }

    private function elemColumns($elem) {
        if (is_object($elem)) {
            $columns = array();
            foreach ($elem as $th => $td) {
                $columns[] = $th;
            }
        } elseif (is_array($elem)) {
            $columns = array_keys($elem);
        } else {
            $columns = null;
        }
        return $columns;
    }

    private function headerDisplay($columns, $doEncoding = true) {
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
        return $rowDisplay;
    }

    private function cellDisplay($rowIndex, $colIndex, $colName, $cellValue, $doEncoding = true) {
        $colNameValue = $this->stringDisplay($colName, $doEncoding);
        $cellValue = $this->valueDisplay($cellValue, $doEncoding);
        $cellDisplay = $this->formatCell($rowIndex, $colIndex, $colNameValue, $cellValue);
        return $cellDisplay;
    }

    protected function objectDisplay($object, $doEncoding = true) {
        $columns = $this->elemColumns($object);
        $hasColumns = !empty($columns);
        $hasHeader = $hasColumns && is_string($columns[0]);
        $headerValue = '';
        if ($hasHeader) {
            $headerValue = $this->headerDisplay($columns, $doEncoding = true);
        }

        $colIndex = 0;
        $rowValue = '';
        foreach ($object as $colName => $cellValue) {
            $rowValue .= $this->cellDisplay(0, $colIndex, $colName, $cellValue, $doEncoding);
            $colIndex++;
        }
        $bodyValue = '';
        if (!empty($rowValue)) {
            $bodyValue = $this->formatRow(0, $rowValue);
        }

        $tableDisplay = $this->formatTable($headerValue, $bodyValue);
        return $tableDisplay;
    }

    protected function arrayDisplay(array $array, $doEncoding = true) {
        if (empty($array)) {
            $tableDisplay = $this->stringDisplay('aucun élément', $doEncoding);
        } else {
            // Les titres des colonnes sont affichées si le tableau est multi-colonnes;
            // c'est le cas si les lignes elles-mêmes sont des tableaux ou des objets.
            // Les titres des colonnes sont alors les keys ou les attributs de la première ligne.
            // Toutes les lignes sont censées avoir la même structure; le comportement n'est pas
            // déterminé dans le cas contraire.
            $headerValue = '';
            $bodyValue = '';

            $arrayKeys = array_keys($array);
            $firstKey = $arrayKeys[0];
            $isListe = !is_string($firstKey);
            if ($isListe) {
                $firstElem = $array[$firstKey];
                $columns = $this->elemColumns($firstElem);
                $hasColumns = !empty($columns);
                $hasHeader = $hasColumns && is_string($columns[0]);
                if ($hasHeader) {
                    $headerValue = $this->headerDisplay($columns, $doEncoding);
                }
                $rowIndex = 0;
                foreach ($array as $tr) {
                    if ($hasColumns) {
                        $isObject = is_object($tr);
                        $colIndex = 0;
                        $rowValue = '';
                        foreach ($columns as $colName) {
                            if ($isObject) {
                                $cellValue = isset($tr->{$colName}) ? $tr->{$colName} : '';
                            } else {
                                $cellValue = isset($tr[$colName]) ? $tr[$colName] : '';
                            }
                            $rowValue .= $this->cellDisplay($rowIndex, $colIndex, $colName, $cellValue, $doEncoding);
                            $colIndex++;
                        }
                    } else {
                        $rowValue = $this->cellDisplay($rowIndex, 0, null, $tr, $doEncoding);
                    }
                    $rowDisplay = $this->formatRow($rowIndex, $rowValue);
                    $bodyValue .= $rowDisplay;
                    $rowIndex++;
                }
            } else {
                $columns = $this->elemColumns($array);
                $hasColumns = !empty($columns);
                $hasHeader = $hasColumns && is_string($columns[0]);
                if ($hasHeader) {
                    $headerValue = $this->headerDisplay($columns, $doEncoding);
                }
                $colIndex = 0;
                $rowValue = '';
                foreach ($array as $colName => $cellValue) {
                    $rowValue .= $this->cellDisplay(0, $colIndex, $colName, $cellValue, $doEncoding);
                    $colIndex++;
                }
                $rowDisplay = $this->formatRow(0, $rowValue);
                $bodyValue .= $rowDisplay;
            }
            $tableDisplay = $this->formatTable($headerValue, $bodyValue);
        }
        return $tableDisplay;
    }

    public function hyperlinkDisplay($value, $url, $doEncoding = true) {
        $valueDisplay = $this->valueDisplay($value, $doEncoding);
        $result = '<a href="' . $url . '" target="_blank">' . $valueDisplay . '</a>';
        return $result;
    }

}
