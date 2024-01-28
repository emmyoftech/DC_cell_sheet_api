<?php
    /**
     * @param int $condition
     *  Takes a number either 1 or 0 stating weather the message was good or bad,
     *  this is for simple response back to the client application   
     *
     * @param string $response_msg
     *  This is for simple response back to the client application
     *
     * @throws Error
     * throws error if condition is not 1 or 0
     * 
     * @return string 
     *  This returns an array formart of a condition and response message
     */

    function conResponse($condition, $response_msg){
        if($condition < 0 || $condition > 1) throw new Error("condition must be either 1 or 0"); 
        return array("condition" => $condition, "message" => $response_msg);
    }
?>