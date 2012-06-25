<?php
    require_once(dirname(__FILE__) . '/configuration.php');
    require_once(dirname(__FILE__) . '/working_folder.php');
    
    class Installer {
        
        function isInstalled() {
            $configuration = &Configuration::instance();
            return $configuration->isSaved();
        }
        
        function install($location) {
            $location = preg_replace('|/$|', '', realpath($location));
            $configuration = &Configuration::instance();
            $configuration->setRepositoryPath($location);
            $configuration->save();
            $folder = new WorkingFolder("$location/repository");
        }
        
        function uninstall($location) {
            $location = preg_replace('|/$|', '', realpath($location));
            $configuration = &Configuration::instance();
            if ($location != $configuration->getRepositoryPath()) {
                return;
            }
            $location = $configuration->getRepositoryPath();
            $configuration->remove();
            $folder = new WorkingFolder("$location/repository");
            $folder->deleteAll();
        }
        
        function getRepositoryPath() {
            $configuration = &Configuration::instance();
            return $configuration->getRepositoryPath();
        }
    }
?>