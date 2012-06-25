<?php
    if (! defined('RUNNER')) {
        define('RUNNER', $_SERVER['SCRIPT_FILENAME']);
        require_once('simpletest/unit_tester.php');
        require_once('simpletest/mock_objects.php');
        require_once('simpletest/web_tester.php');
        require_once('simpletest/reporter.php');
        
        $test = new GroupTest(basename($_SERVER['SCRIPT_FILENAME']));
        $test->addTestFile($_SERVER['SCRIPT_FILENAME']);
        if (SimpleReporter::inCli()) {
            exit ($test->run(new TextReporter()) ? 0 : 1);
        }
        $test->run(new HtmlReporter());
        exit();
    }
?>
