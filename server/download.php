<?php
    require_once(dirname(__FILE__) . '/../classes/arbiter.php');
    require_once(dirname(__FILE__) . '/../classes/configuration.php');
    require_once(dirname(__FILE__) . '/../classes/document.php');

    $arbiter = &new Arbiter(Configuration::instance());
    $document = $arbiter->downloadRequirement($_GET['d']);

    header('Content-Type: application/msword');
    header('Content-Disposition: attachment; filename="' . $_GET['d'] . '.rtf"');
    header('Cache-Control: no-cache');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Content-Length: '. strlen($document->getRaw()));
    
	echo $document->getRaw();
?>