<?php
    class Configuration {
        var $entries;
        var $is_saved;
        var $file;
        
        function &instance($reload = false) {
            static $instance;
            if (! isset($instance) || $reload) {
                $instance = array(new Configuration(dirname(__FILE__) .
                                              '/../configuration/arbiter.conf'));
            }
            return $instance[0];
        }
        
        function Configuration($file) {
            $this->file = $file;
            if (file_exists($this->file)) {
                $this->entries = parse_ini_file($this->file);
                $this->is_saved = true;
            } else {
                $this->entries = array();
                $this->is_saved = false;
            }
        }
        
        function setRepositoryPath($path) {
            $this->entries['repository_path'] = $this->removeTrailingSlash($path);
        }
        
        function getRepositoryPath() {
            if (! isset($this->entries['repository_path'])) {
                return false;
            }
            return $this->entries['repository_path'];
        }
        
        function &getRepositoryFolder() {
            if ($path = $this->getRepositoryPath()) {
                return new WorkingFolder("$path/repository");
            }
        }
        
        function setTestPath($path) {
            $this->entries['tests_path'] = $this->removeTrailingSlash($path);
        }
        
        function getTestPath() {
            if (! isset($this->entries['tests_path'])) {
                return false;
            }
            return $this->entries['tests_path'];
        }
        
        function &getTestFolder() {
            if ($path = $this->getTestPath()) {
                return new WorkingFolder("$path/tests");
            }
        }
        
        function isSaved() {
            return $this->is_saved;
        }
        
        function save() {
            $handle = fopen($this->file, 'w+');
            foreach ($this->entries as $key => $value) {
                fwrite($handle, "$key = $value\n");
            }
            fclose($handle);
            $this->is_saved = true;
        }
        
        function remove() {
            @unlink($this->file);
            $this->entries = array();
            $this->is_saved = false;
        }
        
        function removeTrailingSlash($path) {
            return preg_replace('|/$|', '', $path);
        }
    }
?>
