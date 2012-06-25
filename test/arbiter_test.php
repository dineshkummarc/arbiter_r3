<?php
    include(dirname(__FILE__) . '/run_me_if_no_runner.php');
    require_once(dirname(__FILE__) . '/../classes/arbiter.php');
    require_once(dirname(__FILE__) . '/../classes/repository.php');
    require_once(dirname(__FILE__) . '/../classes/document.php');
    require_once(dirname(__FILE__) . '/../classes/configuration.php');
    
    Mock::generate('Repository');
    Mock::generate('Configuration');
    Mock::generate('Document');
    Mock::generatePartial('Arbiter', 'PartialArbiter', array('createRepository'));

    class TestOfArbiter extends UnitTestCase {
        
        function testNullPostAddsNoDocuments() {
            $configuration = &new MockConfiguration($this);
            
            $repository = &new MockRepository($this);
            $repository->expectNever('write');
            
            $arbiter = &new PartialArbiter($configuration);
            $arbiter->setReturnReference('createRepository', $repository);
            $arbiter->Arbiter($configuration);
            
            $arbiter->handleUpload(array(), array());
        }
        
        function testNonRtfExtensionSetsError() {
            $configuration = &new MockConfiguration($this);
            
            $repository = &new MockRepository($this);
            $repository->expectNever('write');
            
            $arbiter = &new PartialArbiter($configuration);
            $arbiter->setReturnReference('createRepository', $repository);
            $arbiter->Arbiter($configuration);
            
            $arbiter->handleUpload(array(1), array('document' => array('name' => 'file.php')));
            $this->assertEqual($arbiter->getLastUploadError(),
                               'Sorry... your document is not a valid .rtf file');
        }
        
        function testBadMimeTypeSetsError() {
            $configuration = &new MockConfiguration($this);
            
            $repository = &new MockRepository($this);
            $repository->expectNever('write');
            
            $arbiter = &new PartialArbiter($configuration);
            $arbiter->setReturnReference('createRepository', $repository);
            $arbiter->Arbiter($configuration);
            
            $arbiter->handleUpload(array(1),
                                   array('document' => array('name'=> '.rtf', 'type' => 'badMime')));
            $this->assertEqual($arbiter->getLastUploadError(),
                               'Sorry... your document is not a valid .rtf file');
        }
        
        function testGoodFileIsWrittenToRepository() {
            $configuration = &new MockConfiguration($this);
            $document = new MockDocument($this);
            
            $repository = &new MockRepository($this);
            $repository->expectOnce('write', array($document));
            $repository->expectOnce('parse',
                                    array(file_get_contents('../test/samples/title_only.openoffice1.rtf')));
            $repository->setReturnValue('parse', $document);
            
            $arbiter = &new PartialArbiter($configuration);
            $arbiter->setReturnReference('createRepository', $repository);
            $arbiter->Arbiter($configuration);
            
            $arbiter->handleUpload(array(1),
                                   array('document' => array('name'=> 'title_only.openoffice1.rtf',
                                                             'type' => 'text/rtf',
                                                             'tmp_name' => '../test/samples/title_only.openoffice1.rtf')));
        }
    }
?>
