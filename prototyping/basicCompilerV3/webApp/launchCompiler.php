<?php
    //launch a container with compiler app in it for user to use

    //get sdk
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/docker_php/vendor/autoload.php");

    use Spatie\Docker\DockerContainer;

    $imageName = "compiler_app:1.0";

    $hostPort = 5000;

    $containerPort = 8080;

    $containerInstance = DockerContainer::create($imageName)
        ->mapPort($hostPort, $containerPort)
        ->start();
?>