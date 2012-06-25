<?php
    include(dirname(__FILE__) . '/run_me_if_no_runner.php');

    class TestOfInstaller extends WebTestCase {
        
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
        
        function install() {
            $this->get($this->getInstallerUrl());
            $this->setField('location', dirname(__FILE__) . '/../temp/');
            $this->clickSubmit('Create repository');
        }
        
        function uninstall() {
            $this->get($this->getInstallerUrl());
            $this->setField('confirmation', dirname(__FILE__) . '/../temp/');
            $this->clickSubmit('Remove repository');
        }
        
        function testRemovalCleansOutRepository() {
            $this->install();
            $this->uninstall();
            $this->assertFalse(file_exists(dirname(__FILE__) . '/../configuration/arbiter.conf'));
            $this->assertFalse(is_dir(dirname(__FILE__) . '/../temp/repository/'));
        }
        
        function testInstallationCreatesConfiguration() {
            $this->get($this->getInstallerUrl());
            $this->install();
            $this->assertTrue(file_exists(dirname(__FILE__) . '/../configuration/arbiter.conf'));
            $this->uninstall();
        }
        
        function testInstallationAllowsUninstall() {
            $this->get($this->getInstallerUrl());
            $this->assertWantedText('You need to create a new repository');
            $this->assertNoUnwantedText('uninstall');
            $this->install();
            $this->assertNoUnwantedText('You need to create a new repository');
            $this->assertWantedText('this will delete all');
            $this->uninstall();
        }
    }
?>