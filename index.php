<?php
    include_once "./scripts/securityMidWare.php";
    include_once "./scripts/myResponse.php";

    securityMidWare(["get"]);


    echo myResponse("hello");
?>