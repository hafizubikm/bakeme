<?php

// all_tests.php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

$test = &new TestSuite('');
$test->addTestFile('SwxAssemblerTest.php');
$test->run(new SwxReporter());

?>