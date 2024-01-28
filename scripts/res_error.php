<?php
    /**
     * @param string $errmsg 
     *  The caught error message
     * @param int $errcode
     *  The error number of caught error
     * @return array
     *  Returns array representation or error object
     */

     function res_error($errmsg, $errcode){
        return array("message" => $errmsg, "code" => $errcode);
     }
?>