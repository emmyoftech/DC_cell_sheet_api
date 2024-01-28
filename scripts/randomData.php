<?php
    /**
     * @param array $mixed_array
     * This is the array in wich the random element will be gotten from
     * 
     * @return mixed 
     * Returns random data from array
     */

     function randomData ($mixed_array){
        $random_number = rand(0 , count($mixed_array) - 1);

        return $mixed_array[$random_number];
     }
?>