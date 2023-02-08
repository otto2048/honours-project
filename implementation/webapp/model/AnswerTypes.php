<?php

    class AnswerTypes
    {
        const STRING = 0;
        const INT = 1;
        const FLOAT = 2;

        //get string associated with answer type
        public function getAnswerType($state)
        {
            switch($state)
            {
                case AnswerTypes::STRING:
                    return "String";
                case AnswerTypes::INT:
                    return "Int";
                case AnswerTypes::FLOAT:
                    return "Float";
                default:
                    return "Error finding status";
            }
        }
    }

?>