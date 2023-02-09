<?php

    class ExerciseTypes
    {
        const PRETEST = 0;
        const PRACTICE = 1;
        const POSTTEST = 2;

        //get string associated with exercise type
        public function getExerciseType($state)
        {
            switch($state)
            {
                case ExerciseTypes::PRETEST:
                    return "Pre-test";
                case ExerciseTypes::PRACTICE:
                    return "Practice";
                case ExerciseTypes::POSTTEST:
                    return "Post-test";
                default:
                    return "Error finding status";
            }
        }
    }

?>