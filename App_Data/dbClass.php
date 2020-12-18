<?php
    
    class dbClass
    {
        public $server;
        public $userName;
        public $password;
        public $dbName;
        public $conn;
        public $result;

        public function __construct()
        {
            $this->server = "localhost";
            $this->userName = "root";
            $this->password = "";
            $this->dbName = "php_project_db";
        }

        function connect(){
           
            $this->conn = new mysqli($this->server, $this->userName, $this->password, $this->dbName);
            if($this->conn->connect_error)
            {
                print "<script>alert('Err: ".$this->conn->connect_error."')</script>";
            }
            else{
                return $this->conn;
            }
        }

        function select_op($sql)
        {
            $this->result = $this->conn->query($sql);
            return $this->result;
        }

        function insert_update_delete_op($sql)
        {
           return $this->conn->query($sql);
        }

        function insert($tbl_name,$tmp_arr)
        {
            $count = 0;
            $qry = "insert into `$tbl_name` (";
            foreach($tmp_arr as $key => $val){
                if($key == "cpass")
                {
                    continue 1;
                }
                else
                {
                    $qry .= "`$key`,";
                }
            }
            $qry = rtrim($qry, ", ");
            $qry .= ") values (";
            foreach($tmp_arr as $key => $val)
            {
                if($key == 'cpass')
                    continue 1;
                else
                    $qry .= "'$val',";
            }
            $qry = rtrim($qry, ", ");
            $qry .= ")";
            
            if($this->conn->query($qry))
                return true;
            else
                return false;
        }
    }
    
?>
