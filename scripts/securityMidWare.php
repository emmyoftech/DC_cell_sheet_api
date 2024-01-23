<?php

    function securityMidWare($all_methods_array){
        $num_of_ele = count($all_methods_array);
        if($num_of_ele < 1) throw new Error("array cannot be empty for security purposes");
        
        $if_get_is_allowed = false;
        $if_post_is_allowed = false;

        foreach($all_methods_array as $ele){
            if(strtolower($ele) == "get"){
                $if_get_is_allowed = true;
            }else {
                $if_post_is_allowed = true;
            }
        }

        if(!$if_get_is_allowed && !empty($_GET)){
            throw new Error("invalid request");
        }else if(!$if_post_is_allowed && !empty($_POST)){
            throw new Error("invalid request");
        }
        
        header("Access-Control-Allow-Origin: http://localhost:4200");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    }
?>