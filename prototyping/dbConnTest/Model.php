<?php

    require_once("Connection.php");

    //class to perform basic CRUD operations for any table on the database
    abstract class Model
    {
        //connection object
        protected $conn;

        //SQL string
        protected $sqlStmt;

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

        //get all record from a table
        //on success, return JSON string containing all records
        //on failure, return null
        public function selectAll()
        {
            //prepare query
            $stmt = mysqli_prepare($this->conn->getConnection(), $this->sqlStmt);

            if (!$stmt)
            {
                return null;
            }
            
            //execute
            $querySuccess = mysqli_stmt_execute($stmt);

            if (!$querySuccess)
            {
                //close prepared statement
                mysqli_stmt_close($stmt);

                return null;
            }

            //get the result
            $result = mysqli_stmt_get_result($stmt);

            //close prepared statement
            mysqli_stmt_close($stmt);

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
            }

            return null;
        }

        //gets a record specified by ID parameter
        //on success, return JSON string including record information
        //on failure, return null
        public function select($id)
        {
            //prepare statment
            $stmt = mysqli_prepare($this->conn->getConnection(), $this->sqlStmt);

            if (!$stmt)
            {
                return null;
            }

            //bind parameters with user input
            $bindResult = mysqli_stmt_bind_param($stmt, "i", $id);

            if (!$bindResult)
            {
                //close prepared statement
                mysqli_stmt_close($stmt);

                return null;
            }

            $querySuccess = mysqli_stmt_execute($stmt);

            if (!$querySuccess)
            {
                //close prepared statement
                mysqli_stmt_close($stmt);

                return null;
            }

            //get the result
            $result = mysqli_stmt_get_result($stmt);

            //close prepared statement
            mysqli_stmt_close($stmt);

            if ($result != false)
            {
                if (mysqli_num_rows($result) > 0)
                {
                    $data = mysqli_fetch_assoc($result);
                    return json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
                }
            }

            return null;
        }

        //gets collection of records with a custom WHERE condition
        //parameters: json encoded WHERE condition variables & the types of data being inserted
        //on success, return JSON string containing all records
        //on failure, return null
        public function selectCustom($jsonVariables, $paramTypes)
        {
            //decode variables
            $variables = json_decode($jsonVariables, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare statment
            $stmt = mysqli_prepare($this->conn->getConnection(), $this->sqlStmt);

            if (!$stmt)
            {
                return null;
            }

            //bind parameters with user input
            $bindResult = mysqli_stmt_bind_param($stmt, $paramTypes, ...array_values($variables));

            if (!$bindResult)
            {
                //close prepared statement
                mysqli_stmt_close($stmt);

                return null;
            }

            $querySuccess = mysqli_stmt_execute($stmt);

            if (!$querySuccess)
            {
                //close prepared statement
                mysqli_stmt_close($stmt);

                return null;
            }

            //get the result
            $result = mysqli_stmt_get_result($stmt);

            //close prepared statement
            mysqli_stmt_close($stmt);

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
            }

            return null;
        }

        //gets collection of records with a custom WHERE condition, or an empty array
        //parameters: json encoded WHERE condition variables; the types of data being inserted; a message to display if the collection is empty
        //on success, return JSON string containing all records, or an empty array
        //on failure, return null
        public function selectCustomOrEmpty($jsonVariables, $paramTypes, $emptyMessage = " ")
        {
            //decode variables
            $variables = json_decode($jsonVariables, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare statment
            $stmt = mysqli_prepare($this->conn->getConnection(), $this->sqlStmt);

            if (!$stmt)
            {
                return null;
            }

            //bind parameters with user input
            $bindResult = mysqli_stmt_bind_param($stmt, $paramTypes, ...array_values($variables));

            if (!$bindResult)
            {
                //close prepared statement
                mysqli_stmt_close($stmt);

                return null;
            }

            $querySuccess = mysqli_stmt_execute($stmt);

            if (!$querySuccess)
            {
                //close prepared statement
                mysqli_stmt_close($stmt);

                return null;
            }

            //get the result
            $result = mysqli_stmt_get_result($stmt);

            //close prepared statement
            mysqli_stmt_close($stmt);

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
                    return json_encode(array("default"=>$emptyMessage), JSON_INVALID_UTF8_SUBSTITUTE);
                }
            }

            return null;
        }

        //deletes a record specified by ID parameter
        //on success, return true
        //on failure, return false
        public function delete($id)
        {
            //prepare statement
            $stmt = mysqli_prepare($this->conn->getConnection(), $this->sqlStmt);

            if (!$stmt)
            {
                return false;
            }

            //bind parameters with user input
            $bindResult = mysqli_stmt_bind_param($stmt, "i", $id);

            if (!$bindResult)
            {
                //close prepared statement
                mysqli_stmt_close($stmt);
                
                return false;
            }

            $querySuccess = mysqli_stmt_execute($stmt);

            //close prepared statement
            mysqli_stmt_close($stmt);

            //return true/false based on whether query succeeded
            return $querySuccess;
        }

        //creates a record
        //parameters: json encoded record data & the types of the data being inserted, eg "iss", optional parameter to hold the error code of the query executed
        //the order of the json data and the params must match up so that the data is unpacked correctly
        //on success, return the new record ID
        //on failure, return -1
        public function create($jsonData, $paramTypes, &$errorCode = 0)
        {
            //decode data
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare query
            $stmt = mysqli_prepare($this->conn->getConnection(), $this->sqlStmt);

            if (!$stmt)
            {
                return -1;
            }

             //bind parameters with user input
            $bindResult = mysqli_stmt_bind_param($stmt, $paramTypes, ...array_values($data));

            if (!$bindResult)
            {
                //close prepared statement
                mysqli_stmt_close($stmt);

                return -1;
            }

            $querySuccess = mysqli_stmt_execute($stmt);
            
            //return value
            $retValue = -1;

            //if insert succeeded
            if ($querySuccess)
            {
                //get insert ID
                $retValue = $stmt->insert_id;
            }

            $errorCode = mysqli_errno($this->conn->getConnection());

            //close prepared statement
            mysqli_stmt_close($stmt);

            //return ID
            return $retValue;
        }

        //updates a record
        //parameters: json encoded record data & the types of the data being updated, eg "iss"
        //the order of the json data and the params must match up so that the data is unpacked correctly
        //on success, return the ID of the updated record
        //on failure, return -1
        public function update($jsonData, $paramTypes)
        {
            //decode data
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare statement
            $stmt = mysqli_prepare($this->conn->getConnection(), $this->sqlStmt);

            if (!$stmt)
            {
                return -1;
            }

            //bind parameters with user input
            $bindResult = mysqli_stmt_bind_param($stmt, $paramTypes, ...array_values($data));

            if (!$bindResult)
            {
                //close prepared statement
                mysqli_stmt_close($stmt);

                return -1;
            }

            $querySuccess = mysqli_stmt_execute($stmt);
            
            //return value
            $retValue = -1;

            //if update succeeded
            if ($querySuccess)
            {
                //get ID
                //TODO: change this since not all tables are identified by one id column
                $retValue = $data["id"];
            }

            //close prepared statement
            mysqli_stmt_close($stmt);

            //return ID
            return $retValue;
        }
    }

?>