<?php

    class DB{
        private $host = 'localhost';
        private $user = 'root';
        private $password = '';
        private $dbname = 'student';

        public function connect(){
            $con = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
            
            if (!$con) {
                die("Connection failed: " . mysqli_connect_error());
            }
            return $con;
        }

        public function closeConnection($con){
            mysqli_close($con);
        }
    }