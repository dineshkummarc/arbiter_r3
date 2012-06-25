<?php
    class UseCase extends WebTestCase {

        function setUp() {
            $this->deleteConfiguration();
            $this->install();
        }
        
        function tearDown() {
            $this->uninstall();
        }
        
        function deleteConfiguration() {
            @unlink(dirname(__FILE__) . '/../configuration/arbiter.conf');
        }

        function install() {
            $this->get($this->getInstallerUrl());
            $this->setField('location', dirname(__FILE__) . '/../temp/');
            $this->clickSubmit('Create repository');
            $this->assertWantedText('Uninstall the repository');
        }
        
        function uninstall() {
            $this->get($this->getInstallerUrl());
            $this->setField('confirmation', dirname(__FILE__) . '/../temp/');
            $this->clickSubmit('Remove repository');
            $this->assertWantedText('Create a new Repository');
        }
        
        function getSelfUri() {
            if (isset($_SERVER['SCRIPT_URI'])) {
                return $_SERVER['SCRIPT_URI'];
            }
            if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['PHP_SELF'])) {
                return 'http://'. $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            }
			return false;
        }
        
        function getInstallerUrl() {
            static $has_warned = false;

            if ($here = $this->getSelfUri()) {
                return preg_replace('|arbiter/test/.*|', 'arbiter/server/installer.php', $here);
            }
            if (! $has_warned) {
                $has_warned = true;
                trigger_error('Must have server URL as argument');
            }
        }
		
        function getServerUri() {
            static $has_warned = false;

            if (isset($argv[1])) {
                return $argv[1];
            }
            if ($self = $this->getSelfUri()) {
                return preg_replace('|arbiter/test/.*|', 'arbiter/server/index.php', $self);
            }
            if (! $has_warned) {
                $has_warned = true;
                trigger_error('Must have server URL as argument');
            }
        }
    }
?>