<?php
    include(dirname(__FILE__) . '/run_me_if_no_runner.php');
    require_once(dirname(__FILE__) . '/../classes/configuration.php');
    require_once(dirname(__FILE__) . '/../classes/working_folder.php');
    
    class TestOfConfiguration extends UnitTestCase {
        
        function tearDown() {
            $configuration = &new Configuration(dirname(__FILE__) . '/../temp/test.ini');
            $configuration->remove();
            $this->assertFalse(file_exists(dirname(__FILE__) . '/../temp/test.ini'));
        }
        
        function testRepositoryPathWrittenOutCanBeReadBack() {
            $configuration = &new Configuration(dirname(__FILE__) . '/../temp/test.ini');
            $configuration->setRepositoryPath(dirname(__FILE__) . '/../temp/here');
            $this->assertEqual($configuration->getRepositoryPath(),
                               dirname(__FILE__) . '/../temp/here');
            $configuration->save();
            
            $configuration = &new Configuration(dirname(__FILE__) . '/../temp/test.ini');
            $this->assertEqual($configuration->getRepositoryPath(),
                               dirname(__FILE__) . '/../temp/here');
        }
        
        function testCanSetRepositoryFolder() {
            $configuration = &new Configuration(dirname(__FILE__) . '/../temp/test.ini');
            $configuration->setRepositoryPath(dirname(__FILE__) . '/../temp/');
            $configuration->save();
            
            $configuration = &new Configuration(dirname(__FILE__) . '/../temp/test.ini');
            $folder = $configuration->getRepositoryFolder();
            $this->assertIsA($folder, 'WorkingFolder');
            $this->assertEqual($folder->getPath(), dirname(__FILE__) . '/../temp/repository');
        }
        
        function testTestPathWrittenOutCanBeReadBack() {
            $configuration = &new Configuration(dirname(__FILE__) . '/../temp/test.ini');
            $configuration->setTestPath(dirname(__FILE__) . '/../temp/here');
            $this->assertEqual($configuration->getTestPath(),
                               dirname(__FILE__) . '/../temp/here');
            $configuration->save();
            
            $configuration = &new Configuration(dirname(__FILE__) . '/../temp/test.ini');
            $this->assertEqual($configuration->getTestPath(),
                               dirname(__FILE__) . '/../temp/here');
        }
        
        function testCanSetTestGenerationFolder() {
            $configuration = &new Configuration(dirname(__FILE__) . '/../temp/test.ini');
            $configuration->setTestPath(dirname(__FILE__) . '/../temp/');
            $configuration->save();
            
            $configuration = &new Configuration(dirname(__FILE__) . '/../temp/test.ini');
            $folder = $configuration->getTestFolder();
            $this->assertIsA($folder, 'WorkingFolder');
            $this->assertEqual($folder->getPath(), dirname(__FILE__) . '/../temp/tests');
        }
        
        function testOnlyOneInstanceFromSingletonInterface() {
            $this->assertReference(Configuration::instance(),
                                   Configuration::instance());
            $this->assertIsA(Configuration::instance(), 'Configuration');
        }
        
        function testConfigurationFolderIsWritable() {
            $filename = uniqid('temp');
            $handle = fopen(dirname(__FILE__) . "/../configuration/$filename", 'w+');
            fwrite($handle, 'Delete me');
            fclose($handle);
            $this->assertEqual(file_get_contents(dirname(__FILE__) . "/../configuration/$filename"),
                               'Delete me',
                               'The configuration folder must be writable');
            @unlink(dirname(__FILE__) . "/../configuration/$filename");
        }
        
        function testTempFolderIsWritable() {
            $filename = uniqid('temp');
            $handle = fopen(dirname(__FILE__) . "/../temp/$filename", 'w+');
            fwrite($handle, 'Delete me');
            fclose($handle);
            $this->assertEqual(file_get_contents(dirname(__FILE__) . "/../temp/$filename"),
                               'Delete me',
                               'The temp folder must be writable');
            @unlink(dirname(__FILE__) . "/../temp/$filename");
        }
    }
?>