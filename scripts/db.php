<?php

class DB_API {

    private $database_connection;

    public $is_db_connected;

    public $error_msg = "";

    // CREATE TABLE PROPERTIES
    public static $VARCHAR100 = "varchar(100)";

    public static $VARCHAR200 = "varchar(200)";

    public static $INT = "int";

    public static $TEXT_TYPE = "text";

    public static $BOOLEAN_TYPE = "boolean";

    public static $NOT_NULL = "NOT NULL";
    
    public static $UNIQUE = "UNIQUE";

    // DATABASE 

    public function __construct(){
        $this -> database_connection = new mysqli("localhost","root","","dc_cell_sheet_db");
        $this -> is_db_connected = $this-> database_connection -> connect_error ? false : true;

    }

    /**
     * @param string $querystring
     * Takes a SQL query and runs it
     * @return boolean
     */
    private function runQuery($querystring){
        return $this-> database_connection -> query($querystring);
    }

    /**
     * @param string $schema_name
     *  Takes name of schema and will be the prefix of file name in schema directory
     * @param array $array_schema
     *  Takes an array representation of the table colounms
     * @return void 
     */
    private function create_schema($schema_name,$array_schema){
        $jsonifiedschema = json_encode($array_schema);
        file_put_contents("./schema/$schema_name.json", $jsonifiedschema);
    }

    /**
     * @param string $schema_name
     *  Takes the name of table that the schema is being requested
     * @return array 
     *  returns the schema in an array format
     */
    private function get_schema($schema_name){
        return json_decode(file_get_contents("./schema/$schema_name.json"));
    }
    
    /**
     * @param string $schema_name
     *  Takes the name of schema to be deleted
     * @return boolean
     */
    private function delete_schema($schema_name){
        $file_path = "./schema/$schema_name.json";
        if(file_exists($file_path)){
            return unlink($file_path);
        }
        return false;
    }

    /**
     * @param string $tablename
     * The name of the table that will be created
     * @example
     * Table name = users
     * 
     * @param array $colunms
     * This must be an associative array showing the key as colounm name and value as colunm data type
     * colunm array must be multidimentional e.g array(array(name,type,attr))
     * @example
     * array(array("firstname", "varchar(100)", NOT NULL))
     * if you want the id primary key to be AUTO INCREAMENTED the key must "id" and type must be "int" and attr "primary key"  
     *
     * @return boolean
     */
    public function create($tablename,$colunms){
        $table_created = false;
        
        if(count($colunms) == 0) throw new Exception("tables must have colunms");
        
        $colunm_table_string = "id int PRIMARY KEY AUTO_INCREMENT,";

        $colunm_count = count($colunms);

        foreach($colunms as $colunm){
            if($colunm[0] && !$colunm[1]) throw new Exception("must give '$colunm[0]' colunm a type");
            $if_attr_colunm = array_key_exists(2, $colunm) ? $colunm[2] : "";
            $comma = $colunm_count > 1 ? ",": "";
            $colunm_table_string.= "$colunm[0] $colunm[1] $if_attr_colunm $comma";
            $colunm_count--;
        }

        $query = "CREATE TABLE $tablename ($colunm_table_string);";
        
        if($this-> is_db_connected){
            if(!$this -> runQuery($query)){
                $this -> error_msg = "Mwssage: ". $this -> database_connection -> connect_error. " Code: ". $this -> database_connection -> errno ;
            }else{
                $this-> create_schema($tablename, $colunms);
                $table_created = true;
            }
        }else{
            throw new Exception("database is not connected", $this-> database_connection -> errno);
        }
        return $table_created;
    }

    /**
     * @param string $tablename
     * Type of property being dropped Table
     * @return boolean
     */
    public function drop($tablename){
        $done = false;
        $query = "DROP TABLE $tablename";
        if($this-> runQuery($query)){
           $done = $this-> delete_schema($tablename);
        }else{
            throw new Exception("Failed to drop table $tablename");
        }
        return $done;
    }

    /**
     * @param string $tablename
     *  The name of the table that the row will be deleted from
     * @param string $colunm_name
     *  The name colunm that will be matched to get the right record
     * @param mixed $value
     *  The value that will be matched to get the right record
     * @return boolean 
     * @note
     *  If colunm name is not given the table will be truncated
     */
    public function delete($tablename, $colunm_name = null, $value = null){
        $query = $colunm_name == null ? "TRUNCATE TABLE $tablename": "DELETE FROM $tablename WHERE $colunm_name=$value";
        if($colunm_name != null && $value == null) throw new Exception("value parameter cannot be left empty");
        return $this-> runQuery($query);
    }

    /**
     * @param string $tablename
     *  Enter the name of the table you want to insert data to
     * @param array $kvp
     *  This must be a key and value pair, key being name of colunm and the other is the value
     * @return boolean 
     *  This retuns true if the process was successful
     * @description
     *  It creates the table if it doesnt exist
     */
    public function insert($tablename , $kvp){
        $it_worked = false;
        if(count($kvp) < 1 || !$kvp) throw new Exception("cannot give empty key value pair array");
        $kvp_string = "";
        $count = count($kvp);
        $colunms_string = "";
        $values_string = "";

        foreach($kvp as $key => $value){
            if($count == 1){
                $colunms_string .= $key;
                $values_string .= " '".$value."'";
            }else{
                $colunms_string .= $key.",";
                $values_string .= " '".$value."',";
            }
            $count--;
        }

        $query = "INSERT INTO $tablename ($colunms_string) VALUES ($values_string);";

        try{
            $it_worked = $this->runQuery($query);
        }catch (Exception $err){
            if($err -> getCode() == 1146){
               if($this -> create($tablename, $this -> get_schema($tablename))){
                $it_worked = $this -> runQuery($query);
               }
            }else{
                $this-> error_msg = $err -> getMessage();
            }
        }

        return $it_worked;
    }

    /**
     * @param string $tablename
     *  Name of the table where the data lies
     * @param array $kvp
     *  A key and value pair of the coulunm(s) that should be updated
     * @param string $constraint_colunm_name
     *  Colunm that has the values to be update, that will be compared against $constrint_value for accurate deduction
     * @param mixed $constraint_value
     *  Value that will be compared to the $constraint_colunm_name for accurate deduction
     * @return boolean
     */
    public function update($tablename, $kvp, $constraint_colunm_name, $constraint_value){
        if(count($kvp) == 0 || $kvp == null) throw new Exception("key value pair fields cannot be left empty");
        $full_query = "";
        $set_string = "";
        $count = count($kvp);

        foreach($kvp as $key => $value){
            if($count == 1){
                $set_string .= "$key='$value'";
            }else{
                $set_string .= "$key='$value',";
            }
            $count--;
        }

        $full_query = "UPDATE $tablename SET $set_string WHERE $constraint_colunm_name= '$constrint_value'";

        return $this-> runQuery($full_query);
    }
    /**
     * @param string $tablename
     *  The name of table to get the record
     * @param string $constraint_colunm_name
     *  Colunm that has the values to be update, that will be compared against $constrint_value for accurate deduction
     * @param mixed $constraint_value
     *  Value that will be compared to the $constraint_colunm_name for accurate deduction
     * @param boolean|null $all
     *  Specifies wether to return all rows that me requirement or just the first
     * @return array|object|null
     *  Returns the row(s) that meets the requirements or null if nothing found 
     */
    public function get($tablename, $constraint_colunm_name, $constraint_value, $all=null){
        $query = "SELECT * FROM $tablename WHERE $constraint_colunm_name='$constraint_value'";

        $reuslt_opertion = $this-> database_connection -> query($query);

        $result_array = array();
        
        if($reuslt_opertion -> num_rows > 0){
            while($row = $reuslt_opertion -> fetch_assoc()){
                array_push($result_array,$row);
            }
        }
        $result;

        if($all == null){
            $result = key_exists(0,$result_array) ? $result_array[0] : null;
        }else{
            $result = key_exists(0,$result_array) ? $result_array : null;
        }
        return $result;
    }

    /**
     * @param string $tablename
     * Name of the table to get all its records
     * @return array|null
     * Returns null if nothing is found
     */
    public function getAll($tablename){
        $query = "SELECT * FROM $tablename";

        $result_array = array();

        $result_of_query = $this-> database_connection -> query($query);

        if($result_of_query -> num_rows > 0){
            while($row = $result_of_query -> fetch_assoc()){
                array_push($result_array,$row);
            }
        }

        return array_key_exists(0,$result_array) ? $result_array : null;
    }

    /**
     * @param string $tablename
     *  Name of the table to select colunms from
     * @param array $colunm_names
     *  An array of colunm(s) that will be return from the table
     * @example
     *  array("colunm","colunm")
     * @param string|null $constraint_colunm_name
     *  Colunm that has the values to be update, that will be compared against $constrint_value for accurate deduction
     * @param string|null $constraint_value
     *  Value that will be compared to the $constraint_colunm_name for accurate deduction
     * @param boolean|null $all
     *  If true will return all records that meet the requirements
     * @return array|null
     *  Returns an array of requested records
     */
    public function  getAllColunms($tablename, $colunm_names,$constraint_colunm_name=null,$constraint_value=null, $all=null){
        $colunm_string = "";
        
        $count = count($colunm_names);
        
        foreach($colunm_names as $names){
            if($count > 1){
                $colunm_string .= $names.",";
            }else{
                $colunm_string .= $names;
            }
            $count --; 
        }
        $query = "SELECT $colunm_string FROM $tablename";

        if($constraint_colunm_name != null && $constraint_value != null){
            $query.= " WHERE $constraint_colunm_name='$constraint_value'";
        }

        echo $query;

        $result = $this-> database_connection -> query($query);

        $result_array = array();

        if($result -> num_rows > 0){
            while($row = $result -> fetch_assoc()){
                array_push($result_array, $row);
            }
        }

        return $all == null ? (array_key_exists(0,$result_array) ? $result_array[0]: null) : (array_key_exists(0,$result_array) ? $result_array : null);
    }
}
?>