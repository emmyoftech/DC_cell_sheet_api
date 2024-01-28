<?php
    include_once "encrypt.php";
    /**
     * @param string $response
     * This is for simple response back to the client application   
     *
     * @return string 
     * This returns an encrypted form of the response
     */

    function response($response){
        if($response == null) throw new Error("cannot insert null response");
        return json_encode(array("enc_response" => encrypt(json_encode($response))));
    }
?>