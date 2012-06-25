<?php
    // $Id: unit_tests.php,v 1.16 2005/03/30 15:37:51 maetl_ Exp $
    
    if (! defined('RUNNER')) {
        define('RUNNER', __FILE__);
    }
    require_once('simpletest/unit_tester.php');
    require_once('simpletest/mock_objects.php');
    require_once('simpletest/web_tester.php');
    require_once('simpletest/reporter.php');
    
    class UnitTests extends GroupTest {
        function UnitTests() {
            $this->GroupTest('Arbiter Unit tests');
            $this->addTestFile('working_folder_test.php');
            $this->addTestFile('configuration_test.php');
            $this->addTestFile('repository_test.php');
            $this->addTestFile('arbiter_test.php');
			$this->addTestFile('document_parser_test.php');
        }
    }
    
    if (RUNNER == __FILE__) {
        $test = &new UnitTests();
        if (SimpleReporter::inCli()) {
            exit ($test->run(new TextReporter()) ? 0 : 1);
        }
        $test->run(new HtmlReporter());
    }
?>