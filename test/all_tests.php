<?php
    define('RUNNER', __FILE__);
    require_once('simpletest/unit_tester.php');    // Need a symlink to SimpleTest.
    require_once('simpletest/reporter.php');
    require_once('simpletest/mock_objects.php');
    require_once('unit_tests.php');
    
    class AllTests extends GroupTest {
        function AllTests() {
            $this->GroupTest('All tests for Arbiter ' .
                             implode('', file('../VERSION')));
            $this->addTestCase(new UnitTests());
            $this->addTestFile('installer_test.php');
            $this->addTestFile('repository_use_case.php');
            //$this->addTestFile('authentication_test.php');
        }
    }

    $test = &new AllTests();
    if (SimpleReporter::inCli()) {
        exit ($test->run(new TextReporter()) ? 0 : 1);
    }
    $test->run(new HtmlReporter());
?>