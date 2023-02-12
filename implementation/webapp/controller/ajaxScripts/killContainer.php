<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    //kill a container

    //get sdk
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/docker_php/vendor/autoload.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");

    use Spatie\Docker\DockerContainer;
    
    $containerInstance = unserialize($_SESSION["dockerContainer"]);

    if ($containerInstance)
    {
        // $inspectArray = $containerInstance->inspect();
        // var_dump($inspectArray);
        $containerInstance->stop();

        echo 1;
    }
    else
    {
        echo 0;
    }
?>