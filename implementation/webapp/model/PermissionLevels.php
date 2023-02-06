<?php

    class PermissionLevels
    {
        const ADMIN = 3;
        const GUEST = 2;
        const EXPERIMENT = 1;
        const CONTROL = 0;

        //get string value associated with user permission
        public function getPermissionLevel($state)
        {
            switch($state)
            {
                case PermissionLevels::ADMIN:
                    return "Admin Group";
                case PermissionLevels::GUEST:
                    return "Guest Group";
                case PermissionLevels::EXPERIMENT:
                    return "Experimental Group";
                case PermissionLevels::CONTROL:
                    return "Control Group";
                default:
                    return "Error finding status";
            }
        }
    }

    
?>