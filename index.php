<?php
    header("Access-Control-Allow-Origin: http://localhost:4200");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    echo('{"data": "hello"}');
?>