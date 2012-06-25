<?php
    include(dirname(__FILE__) . '/run_me_if_no_runner.php');
    require_once('../classes/working_folder.php');
    require_once('../classes/configuration.php');
    require_once('../classes/document.php');
    require_once('../classes/repository.php');
    
    Mock::generate('WorkingFolder');
    Mock::generate('Configuration');
    Mock::generate('Document');
    
    class RepositoryTest extends UnitTestCase {
        
        function testNewDocumentIsSavedToStorage() {
            $folder = &new MockWorkingFolder($this);
            $folder->expectOnce('write', array('Typical Requirement', 'Stuff'));
            
            $document = &new MockDocument($this);
            $document->setReturnValue('getTitle', 'Typical Requirement');
            $document->setReturnValue('getRaw', 'Stuff');
            
            $repository = &new Repository($folder);
            $repository->write($document);            

            $folder->tally();
        }
        
        function testCanReadExistingDocumentFromStorage() {
            $folder = &new MockWorkingFolder($this);
            $folder->expectOnce('read', array('Typical Requirement'));
            $folder->setReturnValue('read', 'Stuff');
            
            $repository = &new Repository($folder);
            $document = &$repository->read('Typical Requirement');
            $this->assertEqual($document, new Document('Stuff'));

            $folder->tally();
        }
        
        function testNoTitlesInEmptyRepository() {
            $folder = &new MockWorkingFolder($this);
            $folder->setReturnValue('getAllTitles', array());
            
            $repository = &new Repository($folder);
            $this->assertEqual($repository->getAllTitles(), array());
        }
        
        function testTitlesFromFileSystem() {
            $folder = &new MockWorkingFolder($this);
            $folder->setReturnValue('getAllTitles', array('A', 'B'));
            
            $repository = &new Repository($folder);
            $this->assertEqual($repository->getAllTitles(), array('A', 'B'));
        }
    }
?>