<?php
/**
 * @param string $encrypted_data
 * A JSON stringified data string to be encrypted
 * 
 * @return string
 * An encrypted form of inputed data
 */

 function decrypt($encrypted_data){
    $sub_data = substr($encrypted_data, 4, -4);

    $decrypted_data = "";

    for($i = 0; $i < strlen($sub_data); $i += 5) {
        if( $i % 5 == 0) $decrypted_data .= $sub_data[$i];
    };

    $decrypted_data = substr($decrypted_data, 0 , -1). $sub_data[strlen($sub_data) - 1];
    
    return $decrypted_data;
 }
?>