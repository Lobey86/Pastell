<?php

require_once(__DIR__ . "/BLDisplayValue.class.php");

class BLDisplayText extends BLDisplayValue {

    const INDENT = '        ';

    protected function encode($text) {
        $result = utf8_encode($text);
        return $result;
    }

    protected function concat(&$target, &$source) {
        if ($target) {
            $target .= PHP_EOL;
        }
        $target .= $source;
        return $target;
    }

    protected function formatDatetime($datetime) {
        $datetimeUnix = strtotime($datetime);
        if ($datetimeUnix === false) {
            return $datetime;
        }
        return date("d/m/Y H:i:s", $datetimeUnix);
    }

    private function objectOrArrayDisplay($objectOrArray, $indentLevel = 0) {
        $display = '';
        $indent = str_repeat(self::INDENT, $indentLevel);
        foreach ($objectOrArray as $key => $value) {
            if ($display) {
                $display .= PHP_EOL;
            }
            $display .= $indent . '[' . $key . ']';
            if (is_object($value) || is_array($value)) {
                $display .= PHP_EOL;
                $display .= $this->objectOrArrayDisplay($value, $indentLevel + 1);
            } else {
                $display .= ' ' . $value;
            }
        }
        return $display;
    }

    protected function objectDisplay($object, $doEncoding = true) {
        $display = $this->objectOrArrayDisplay($object, 0);
        if ($doEncoding) {
            $display = $this->encode($display);
        }
        return $display;
    }

    protected function arrayDisplay(array $array, $doEncoding = true) {
        if (empty($array)) {
            return $this->stringDisplay('aucun élément', $doEncoding);
        }
        return $this->objectDisplay($array, $doEncoding);
    }

}
