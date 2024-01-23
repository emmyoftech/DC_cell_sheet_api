<?php
    /**
     * @param string $response
     * This is for simple response back to the client application   
     *
     * @return string 
     * This returns an encrypted form of the response
     */

    function myResponse($response){
        return json_encode(array("response" => $response));
    }
?>