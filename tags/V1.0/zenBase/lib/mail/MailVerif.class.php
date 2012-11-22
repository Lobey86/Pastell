<?php
class MailVerif {

    function isValidEmail($email) {
        $regex = '/^[*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+(@[0-9a-z-]+\.)+[0-9a-z]{2,}$/i';
        return preg_match($regex, trim($email), $matches);
    }
}