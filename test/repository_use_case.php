<?php
    include(dirname(__FILE__) . '/run_me_if_no_runner.php');
    require_once(dirname(__FILE__) . '/use_case.php');
    require_once(dirname(__FILE__) . '/../classes/arbiter.php');
    require_once(dirname(__FILE__) . '/../classes/configuration.php');
    
    class RepositoryManagement extends UseCase {
        
        function uploadHack($sample_name) {
            $arbiter = &new Arbiter($configuration = &Configuration::instance(true));
            $raw = file_get_contents(dirname(__FILE__) . "/samples/$sample_name");
            $arbiter->uploadRequirement($raw);
        }
        
        function testServerStartsEmpty() {
            $this->get($this->getServerUri());
            $this->assertWantedPattern('/Waiting for first document/i');
        }
        
        function testCanParseTitleOnlyDocument() {
            $this->uploadHack('title_only.openoffice1.rtf');
            $this->get($this->getServerUri());
            $this->assertWantedPattern('/Title Only/');
            $this->clickLink('Title Only');			
            $this->assertMime(array('application/octet-stream', 'application/msword'));
            $this->assertWantedPattern('/This is an empty requirements document/');
        }
    }
?>