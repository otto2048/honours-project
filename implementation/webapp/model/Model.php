<?php

    require_once("Connection.php");

    //class to perform basic CRUD operations for any table on the database
    abstract class Model
    {
        //connection object
        protected $conn;

        //SQL string
        protected $sqlStmt;

        private $stmt;

        public function __construct()
        {
            //set up connection
            $this->conn = new Connection();            
        }

        public function __destruct()
        {
            //close connection
            $this->conn = null;
        }

        private function bindParameters($jsonVariables, $paramTypes)
        {
            //decode variables
            $variables = json_decode($jsonVariables, JSON_INVALID_UTF8_SUBSTITUTE);

            //bind parameters with user input
            $bindResult = mysqli_stmt_bind_param($this->stmt, $paramTypes, ...array_values($variables));

            if (!$bindResult)
            {
                //close prepared statement
                mysqli_stmt_close($this->stmt);

                return false;
            }

            return true;
        }

        private function executeQuery($closeStatementOnFailure = true)
        {
            $querySuccess = mysqli_stmt_execute($this->stmt);

            if (!$querySuccess)
            {
                if ($closeStatementOnFailure)
                {
                    //close prepared statement
                    mysqli_stmt_close($this->stmt);
                }

                return false;
            }

            return $querySuccess;
        }

        private function runQuery($jsonVariables = null, $paramTypes = null, $closeStatementOnFailure = true)
        {
            //check db conn
            if ($this->conn->getConnection() == null)
            {
                return false;
            }

            //prepare query
            $this->stmt = mysqli_prepare($this->conn->getConnection(), $this->sqlStmt);

            if (!$this->stmt)
            {
                return null;
            }

            //bind result if this query has a WHERE clause or JOIN etc.
            if ($jsonVariables != null)
            {
                $bindResult = $this->bindParameters($jsonVariables, $paramTypes);

                if (!$bindResult)
                {
                    return null;
                }
            }

            //execute
            $querySuccess = $this->executeQuery($closeStatementOnFailure);

            return $querySuccess;
        }

        //get record(s) from a table, or check if the table is empty
        //use the jsonVariables and paramTypes variables to give the statement variables
        //on success, return JSON string containing all records
        //on failure, return null
        public function retrieve($jsonVariables = null, $paramTypes = null, $emptyMessage = " ")
        {
            //run the query
            $querySuccess = $this->runQuery($jsonVariables, $paramTypes);

            if (!$querySuccess)
            {
                //return json_encode(array("isempty"=>$emptyMessage), JSON_INVALID_UTF8_SUBSTITUTE);
                return null;
            }

            //get the result
            $result = mysqli_stmt_get_result($this->stmt);

            //close prepared statement
            mysqli_stmt_close($this->stmt);

            if ($result != false)
            {
                if (mysqli_num_rows($result) > 0)
                {
                    //convert to JSON
                    $rows = array();
                    while($r = mysqli_fetch_assoc($result)) {
                        $rows[] = $r;
                    }

                    return json_encode($rows, JSON_INVALID_UTF8_SUBSTITUTE);
                }
                else
                {
                    return json_encode(array("isempty"=>$emptyMessage), JSON_INVALID_UTF8_SUBSTITUTE);
                }
            }

            //return json_encode(array("isempty"=>$emptyMessage), JSON_INVALID_UTF8_SUBSTITUTE);

            return null;
        }

        //deletes a record specified by primary key
        //on success, return true
        //on failure, return false
        public function delete($jsonVariables, $paramTypes)
        {
            //run the query
            $querySuccess = $this->runQuery($jsonVariables, $paramTypes, false);

            //deal with the results of the query
            //close prepared statement
            mysqli_stmt_close($this->stmt);

            //return true/false based on whether query succeeded
            return $querySuccess;
        }

        //creates a record
        //parameters: json encoded record data & the types of the data being inserted, eg "iss", optional parameter to hold the error code of the query executed
        //the order of the json data and the params must match up so that the data is unpacked correctly
        //on success, return true
        //on failure, return false
        public function create($jsonData, $paramTypes, &$errorCode = 0)
        {
            //run query
            $querySuccess = $this->runQuery($jsonData, $paramTypes, false);

            $errorCode = mysqli_errno($this->conn->getConnection());

            //close prepared statement
            mysqli_stmt_close($this->stmt);

            return $querySuccess;
        }

        //updates a record
        //parameters: json encoded record data & the types of the data being updated, eg "iss"
        //the order of the json data and the params must match up so that the data is unpacked correctly
        //on success, return true
        //on failure, return false
        public function update($jsonData, $paramTypes)
        {
            //run query
            return $this->runQuery($jsonData, $paramTypes);
        }
    }

?>