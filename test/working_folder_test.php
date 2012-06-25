<?php
    include(dirname(__FILE__) . '/run_me_if_no_runner.php');
    require_once(dirname(__FILE__) . '/../classes/working_folder.php');
    
    class WorkingFolderTest extends UnitTestCase {
        
        function setUp() {
            $this->deleteTestFolder();
        }
        
        function tearDown() {
            $this->deleteTestFolder();
        }
        
        function deleteTestFolder() {
            @unlink(dirname(__FILE__) . '/../temp/test_folder/test.txt');
            @rmdir(dirname(__FILE__) . '/../temp/test_folder');
        }
        
        function testFolderIsCreatedAutomaticallyOnFirstWrite() {
            $folder = &new WorkingFolder(dirname(__FILE__) . '/../temp/test_folder');
            $this->assertFalse(file_exists(dirname(__FILE__) . '/../temp/test_folder'));
            $folder->write('test.txt', 'Hello');
            $this->assertTrue(file_exists(dirname(__FILE__) . '/../temp/test_folder'));
        }
        
        function testFileWrittenToFileSystem() {
            $folder = &new WorkingFolder(dirname(__FILE__) . '/../temp/test_folder');
            $folder->write('test.txt', 'Hello');
            $this->assertTrue(file_exists(dirname(__FILE__) . '/../temp/test_folder/test.txt'));
            $this->assertEqual(
                    file_get_contents(dirname(__FILE__) . '/../temp/test_folder/test.txt'),
                    'Hello');
        }
        
        function testCanReadBackFileWrittenToFileSystem() {
            $folder = &new WorkingFolder(dirname(__FILE__) . '/../temp/test_folder');
            $folder->write('test.txt', 'Hello');
            $this->assertEqual($folder->read('test.txt'), 'Hello');
        }
        
        function testReadingBackNonExistentFileGivesFalse() {
            $folder = &new WorkingFolder(dirname(__FILE__) . '/../temp/test_folder');
            $this->assertIdentical($folder->read('a'), false);
        }
        
        function testEmptyInventoryIfNoFolder() {
            $folder = &new WorkingFolder(dirname(__FILE__) . '/../temp/test_folder');
            $this->assertIdentical($folder->getAllTitles(), array());
        }
        
        function testCanReadFileListOfSingleFile() {
            $folder = &new WorkingFolder(dirname(__FILE__) . '/../temp/test_folder');
            $folder->write('test.txt', 'Hello');
            $this->assertIdentical($folder->getAllTitles(), array('test.txt'));
        }
        
        function testNonExistentFolderAppearsEmpty() {
            $folder = &new WorkingFolder('/anywhere');
            $this->assertIdentical($folder->getAllTitles(), array());
        }
    }
?>