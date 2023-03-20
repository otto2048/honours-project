<?php

    class PermissionLevels
    {
        const ADMIN = 4;
        const GUEST = 3;
        const EXPERIMENT = 2;
        const CONTROL = 1;
        const UNASSIGNED = 0;

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
                case PermissionLevels::UNASSIGNED:
                    return "Not yet assigned permission";
                default:
                    return "Error finding status";
            }
        }
    }

    
?>