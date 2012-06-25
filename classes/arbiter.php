<?php
    require_once(dirname(__FILE__) . '/repository.php');
    require_once(dirname(__FILE__) . '/configuration.php');
    
    class Arbiter {
        var $repository;
        var $error = false;
        
        function Arbiter(&$configuration) {
            $this->repository = &$this->createRepository($configuration);
        }
        
        function handleUpload($post, $files) {
            if ($this->documentIsPosted($post, $files)) {
                $file = $files['document'];
                if ($this->isAcceptable($file)) {
                    $this->uploadRequirement(file_get_contents($file['tmp_name']));
                } else {
                    $this->error = "Sorry... your document is not a valid .rtf file";
                }
            }
        }
        
        function getAllTitles() {
            return $this->repository->getAllTitles();
        }
        
        function getDocumentFromTitle($title) {
        }
        
        function uploadRequirement($raw) {
            $this->repository->write($this->repository->parse($raw));
        }
        
        function downloadRequirement($title) {
            return $this->repository->read($title);
        }
        
        function getLastUploadError() {
            return $this->error;
        }
	
        function documentIsPosted($post, $files) {
            return (isset($post) && isset($files['document']));
        }
        
        function isAcceptable($file) {
            $allowedMimeTypes = array(
                    'application/octetstream',
                    'application/msword',
                    'text/rtf');
            return (strstr($file['name'],'.rtf') &&
                    in_array($file['type'], $allowedMimeTypes));
        }
        
        /** @protected */
        function &createRepository(&$configuration) {
            return new Repository($configuration->getRepositoryFolder());
        }
    }
?>