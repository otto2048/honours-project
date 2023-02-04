<?php

    require_once("ConnectionConfig.php");

    //Connection class used to connect to the database
    class Connection
    {
        private $conn;

        public function __construct()
        {
            //on creation, create a new connection
            $this->openConnection();
        }

        public function __destruct()
        {
            //on deletion, close connection
            $this->closeConnection();
        }

        public function getConnection()
        {
            return $this->conn;
        }

        //function to open a connection
        public function openConnection()
        {
            //checking if connection already exists
            if ($this->conn)
            {
                if ($this->conn -> ping())
                {
                    return true;
                }
            }

            //create connection
            $this->conn = mysqli_connect(ConnectionConfig::SERVER_NAME, ConnectionConfig::USERNAME, ConnectionConfig::PASSWORD, ConnectionConfig::DB_NAME);
            
            //check connection was created
            if (!$this->conn)
            {
                return false;
            }

            return true;
        }

        //function to close connection
        public function closeConnection()
        {
            //if connection is open, close it
            if ($this->conn->ping())
            {
                //close the connection
                return $this->conn->close();
            }

            return true;
        }
    }

?>