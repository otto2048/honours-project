<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/docker_php/vendor/autoload.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");

    use Spatie\Docker\DockerContainer;

    class CompilerController
    {
        private $compilerInstanceId;
        private $compilerImageName;
        private $compilerContainerName;
        private $hostPort;
        private $containerPort;

        public function __construct($hostPort = 5000)
        {
            $this->compilerInstanceId = "dockerContainer";

            $this->compilerImageName = "compiler_app:1.1";
            $this->compilerContainerName = "compilerContainer";

            $this->hostPort = $hostPort;

            $this->containerPort = 8080;
        }

        public function launchCompiler()
        {
            $compilerInstance = DockerContainer::create($this->compilerImageName)
            ->name($this->compilerContainerName)
            ->mapPort($this->hostPort, $this->containerPort)
            ->start();

            if ($compilerInstance)
            {
                $_SESSION[$this->compilerInstanceId] = serialize($compilerInstance);

                return true;
            }
            else
            {
                return false;
            }
        }

        public function killCompiler()
        {
            if (isset($_SESSION[$this->compilerInstanceId]))
            {
                //kill container
                $compilerInstance = unserialize($_SESSION[$this->compilerInstanceId]);

                $compilerInstance->stop();

                unset($_SESSION[$this->compilerInstanceId]);
                return true;
            }

            return false;
        }

        public function getCompilerStatus()
        {
            if (isset($_SESSION[$this->compilerInstanceId]))
            {
                $compilerInstance = unserialize($_SESSION[$this->compilerInstanceId]);

                if ($compilerInstance)
                {
                    return true;
                }
            }

            return false;
        }
    }

?>