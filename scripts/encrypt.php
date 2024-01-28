<?php
include_once "randomData.php";

/**
 * @param string $data
 * A JSON stringified data string to be encrypted
 * 
 * @return string
 * An encrypted form of inputed data
 */

    function encrypt($data){
        if(strlen($data) < 1) throw new Error("cannot encrypt empty string");
        
        $encrypt_alphabet_characters_array = str_split("abcdefghijklmnopqrstuvwxvz");
        
        $encrypt_num_characters_array = array(1,2,3,4,5,6,7,8,9);
        
        $encrypt_characters = array_merge($encrypt_alphabet_characters_array, $encrypt_num_characters_array);

        $encrypted_data = "";

        for($i = 0; $i < strlen($data); $i++){
            $seprator = "";

            for($j = 0; $j < 4; $j++) $seprator .= randomData($encrypt_characters);

            if($i == 0 || $i == strlen($data) - 1){
                $encrypted_data .= $seprator. $data[$i]. $seprator;
            }else{
                $encrypted_data .= $data[$i]. $seprator;
            }
        }

        return $encrypted_data;
    }
?>