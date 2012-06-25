<?php
    require_once(dirname(__FILE__) . '/document_parser.php');
	
    /*
	 * document object
	 */
    class Document {
        var $content;
		var $listener;
		
        /*
		 * @param $contents text data
		 */
        function Document($contents) {
			$this->content = $contents;
			$this->listener = &$this->createListener($contents);
        }

		/*
		 * creates a listener and parses the document
		 * @access protected
		 */
		function createListener($contents) {
			$listener = &new RtfListener();
			$parser = &new RtfParser($listener);
			$parser->parse($contents);
			return $listener;
		}

        /*
         * returns the title of the document - if no title
		 * is found, the first line of the document is returned
         * @return string title
         */        
        function getTitle() {
            return $this->listener->getTitle();
        }

        /*
         * returns the raw content of a document
         * @return string text stream
         */
        function getRaw() {
            return $this->content;
        }

        /*
         * returns a list of tests found in the document
         */
        function getAcceptanceTests() {
            return array();
        }
    }
?>