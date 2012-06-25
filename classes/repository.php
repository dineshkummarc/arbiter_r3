<?php
	// $Id: repository.php,v 1.16 2005/03/30 15:36:43 maetl_ Exp $
	
	require_once(dirname(__FILE__) . '/document.php');
    require_once(dirname(__FILE__) . '/working_folder.php');
    
	/**
	 * Base for document repository
	 */
    class Repository {
        var $folder;
        
        function Repository(&$folder) {
            $this->folder = &$folder;
        }
        
        function write(&$document) {
            $this->folder->write($document->getTitle(), $document->getRaw());
        }
        
        function &read($title) {
            return $this->parse($this->folder->read($title));
        }
        
        function getAllTitles() {
            return $this->folder->getAllTitles();
        }
        
        function &parse($contents) {
            return new Document($contents);
        }
    }
?>