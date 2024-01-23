<?php
/**
 * @param string $data
 * A JSON stringified data string to be encrypted
 * 
 * @return string
 * An encrypted form of inputed data
 */

    function encryptData($data){
        if(count($data) < 1) throw new Error("cannot encrypt empty string");
    }
?>