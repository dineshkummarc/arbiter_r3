<?php
	/**
	* Returns modified date along with file contents
	*/
	class DocumentFolder extends WorkingFolder {
		function read($name) {
			$file = $this->path . "/$name";
			return array('content' => @file_get_contents($file),
			             'modified'=> @filemtime($file));
		}
	}

    /**        
    * Implements repository interface for working with filesystem
    */
    class WorkingFolder {
        var $path;

        function WorkingFolder($path) {
            $this->path = preg_replace('|/$|', '', $path);
        }
        
        function getPath() {
            return $this->path;
        }

        /**        
        * returns a file from the folder
        * @return string raw document stream
        */        
        function read($name) {
            return @file_get_contents($this->path . "/$name");
        }

        /**        
        * writes a file to the folder
        * @return int filesize
        */          
        function write($name, $contents) {
            $this->createDirectoryIfNonExistent();
            $file = fopen($this->path . "/$name", 'w');
            $bytes = fwrite($file, $contents);
            fclose($file);
            return ($bytes == strlen($contents));
        }

        /**        
        * removes a file from the folder
        * @return void
        */         
        function delete($name) {
            @unlink($this->path . "/$name");
        }

        /**        
        * gets list of documents in the folder
        * @return array filenames
        */         
        function getAllTitles() {
            if (! file_exists($this->path)) {
                return array();
            }
            return $this->getFiles($this->path);
        }

        /**
        * gets list of files in directory
        * @access private
        */
        function getFiles($path) {
            $files = array();
            if ($handle = opendir($path)) {
               while (false !== ($file = readdir($handle))) {
                   if ($file != "." && $file != "..") {
                       $files[] = $file;
                   }
               }
               closedir($handle);
            }
            return $files;
        }
        
        /**
        * makes a new working directory
        * @access private
        */
        function createDirectoryIfNonExistent() {
            if (! file_exists($this->path)) {
                mkdir($this->path);
            }
        }
        
        function deleteAll() {
            $this->rm($this->path);
        }
        
        /** @private */
		function rm($folder) {
            if (is_dir($folder)) {
                $dir = dir($folder);
			    while ($file = $dir->read()) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    @unlink("$folder/$file");				
		        }
                $dir->close();
                return rmdir($folder);		
            }
        }
    }
?>