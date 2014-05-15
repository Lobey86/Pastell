<?php

require_once(__DIR__ . "/BLDisplayValue.class.php");

class BLDisplayJson extends BLDisplayValue {

    protected function encode($text) {
        // L'encodage est réalisé en amont
        return $text;
    }

    protected function concat(&$target, &$source) {
        if (is_array($target)) {
            $target[] = $source;
        } elseif (isset($target)) {
            $target = array($target, $source);
        } else {
            $target = $source;
        }
        return $target;
    }

    protected function arrayDisplay(array $array, $doEncoding = true) {
        return $array;
    }

    protected function objectDisplay($object, $doEncoding = true) {
        return $object;
    }

}
