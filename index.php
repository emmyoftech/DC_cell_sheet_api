<?php
    include_once "./scripts/securityMidWare.php";
    include_once "./scripts/response.php";
    include_once "./scripts/db.php";
    include_once "./scripts/res_error.php";
    include_once "./scripts/conditionalResponse.php";
    
    securityMidWare(["get"]);

    $db = new DB_API();

    try{
        $users = $db -> getAll("user_table");

        if($users == null){
            die(response($users));
        }else{
            die(response(conResponse(0,"there are no more users")));
        }
    }catch(Exception $err){
        echo  res_error($err -> getMessage(), $err -> getCode());
    }
?>