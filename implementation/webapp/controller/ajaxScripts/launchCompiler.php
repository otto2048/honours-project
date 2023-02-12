<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    //launch a container with compiler app in it for user to use

    //get sdk
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/docker_php/vendor/autoload.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");

    use Spatie\Docker\DockerContainer;

    $imageName = "compiler_app:1.1";
    $containerName = "cont";

    $hostPort = 5000;

    $containerPort = 8080;

    $containerInstance = DockerContainer::create($imageName)
        ->name($containerName)
        ->mapPort($hostPort, $containerPort)
        ->start();

    if ($containerInstance)
    {
        $_SESSION["dockerContainer"] = serialize($containerInstance);

        echo 1;
    }
    else
    {
        echo 0;
    }
?>