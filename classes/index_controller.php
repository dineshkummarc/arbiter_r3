<?php

require_once(dirname(__FILE__) . '/server.php');
require_once(dirname(__FILE__) . '/file_repository.php');
require_once(dirname(__FILE__) . '/simpletest_generator.php');

class IndexController {
	var $responseModel;
	var $post;
	var $files;
	var $allowedMimeTypes = array(
	        'application/octetstream',
	        'application/msword',
	        'text/rtf');
	
	function IndexController(&$responseModel, $post, $files) {
		$this->responseModel =& $responseModel;
		$this->post = $post;
		$this->files = $files;
	}
	
	function execute() {
		//perhaps a good idea to define the paths in another file later on.
		$server = &new Server(
		    new FileRepository(dirname(__FILE__) . '/../temp/acceptance'),
		    new SimpleTestGenerator(dirname(__FILE__) . '/../external/simpletest')
		);

		if ($this->documentIsPosted()) {
			$file = $this->files['document'];
			if ($this->isAcceptable($file)) {
				$server->uploadRequirement(file_get_contents($file['tmp_name']));
			} else {
				$this->responseModel['message'] = "Sorry... your document is not a valid .rtf file";
			}
		}
		
		$this->responseModel['documents'] = $server->getAllTitles();
		$this->responseModel['tests'] = $server->getAllTests();
	}
	
	function documentIsPosted() {
	    return (isset($this->post) && isset($this->files['document']));
	}
	
	function isAcceptable($file) {
	    return (strstr($file['name'], '.rtf') && in_array($file['type'], $this->allowedMimeTypes));
	}
}

?>