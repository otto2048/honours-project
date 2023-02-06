<?php
//including file to find Root directory
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    require_once("../../../findingRootDirectory.php");
}

if (strpos($_SERVER["REQUEST_URI"], "index.php"))
{
    header('Location: '.createFilePath(findRoot(dirname($_SERVER["REQUEST_URI"])))."view/index.php");
}
else
{
    header('Location: '.createFilePath(findRoot($_SERVER["REQUEST_URI"]))."view/index.php");
}
?>