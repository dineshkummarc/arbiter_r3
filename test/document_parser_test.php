<?php
	include(dirname(__FILE__) . '/run_me_if_no_runner.php');
    require_once(dirname(__FILE__) .'/../classes/document_parser.php');

    Mock::generate('RtfParser');

    class RtfScannerTest extends UnitTestCase {
		
		function testEmptyDocumentSendsNothing() {
			$parser = &new MockRtfParser($this);
			$parser->expectNever('acceptStartGroup');
			$parser->expectNever('acceptControl');
			$parser->expectNever('acceptEndGroup');
			
			$scanner = &new RtfScanner($parser);
			$scanner->tokenize('');
			
			$parser->tally();
		}
		
		function testMinimalRtfDocument() {
			$parser = &new MockRtfParser($this);
			$parser->expectOnce('acceptStartGroup');
			$parser->expectArgumentsAt(0, 'acceptControl', array('rtf1'));
			$parser->expectArgumentsAt(1, 'acceptControl', array('ansi'));
			$parser->expectOnce('acceptEndGroup');
			
			$scanner = &new RtfScanner($parser);
			$scanner->tokenize('{\rtf1\ansi}');
			
			$parser->tally();
		}
		
		function testNestingLevelMatchesGroupCalls() {
			$parser = &new MockRtfParser($this);
			$parser->expectCallCount('acceptStartGroup', 2);
			$parser->expectCallCount('acceptEndGroup', 2);
			
			$scanner = &new RtfScanner($parser);
			$scanner->tokenize('{\rtf1\ansi{\info \title This is a Document Title}}');
			
			$parser->tally();
		}
			
		function testNestedCanBeRead() {
			$parser = &new MockRtfParser($this);
			$parser->expectArgumentsAt(0, 'acceptContent', array('This is a Document Title'));
			
			$scanner = &new RtfScanner($parser);
			$scanner->tokenize('{\rtf1\ansi{\info \title This is a Document Title}}');
			
			$parser->tally();
		}
	
		function testTripletTokens() {
			$parser = &new MockRtfParser($this);
			$parser->expectCallCount('acceptStartGroup', 3);
			$parser->expectArgumentsAt(0, 'acceptControl', array('rtf1'));
			$parser->expectArgumentsAt(1, 'acceptControl', array('ansi'));
			$parser->expectArgumentsAt(2, 'acceptControl', array('info'));
			$parser->expectArgumentsAt(3, 'acceptControl', array('title'));
			$parser->expectArgumentsAt(0, 'acceptContent', array('This is a Document Title'));
			$parser->expectArgumentsAt(1, 'acceptContent', array('Plain Text'));
			$parser->expectCallCount('acceptEndGroup', 3);
			
			$raw = '{\rtf1\ansi{\info{\title This is a Document Title}} Plain Text}';
			$scanner = &new RtfScanner($parser);
			$scanner->tokenize($raw);
			
			$parser->tally();
		}
		
		function testMalformedInput() {
			$parser = &new MockRtfParser($this);
			$parser->expectOnce('badInput');
			
			$scanner = &new RtfScanner($parser);
			$scanner->tokenize('{\rtf1\ansi');
			
			$parser->tally();
		}
	}
	
	Mock::generate('RtfListener');
	
	class RtfParserTest extends UnitTestCase {
		
		function testBasicContentEvents() {
			$listener = &new MockRtfListener($this);
			
			$listener->expectArgumentsAt(0, 'startElement', array('rtf1', 1));
			$listener->expectArgumentsAt(0, 'addProperty', array('rtf1', 'ansi'));
			$listener->expectArgumentsAt(1, 'startElement', array('info', 2));
			$listener->expectArgumentsAt(2, 'startElement', array('title', 3));
			$listener->expectArgumentsAt(0, 'addContent', array('This is a Title', 3));
			$listener->expectArgumentsAt(0, 'endElement', array('title'));
			$listener->expectArgumentsAt(1, 'endElement', array('info'));
			$listener->expectArgumentsAt(1, 'addContent', array('Some ', 1));
			$listener->expectArgumentsAt(3, 'startElement', array('i', 2));
			$listener->expectArgumentsAt(1, 'addProperty', array('i', 'big'));
			$listener->expectArgumentsAt(2, 'addContent', array('Italic ', 2));
			$listener->expectArgumentsAt(2, 'endElement', array('i'));
			$listener->expectArgumentsAt(3, 'addContent', array('Text', 1));
			$listener->expectArgumentsAt(3, 'endElement', array('rtf1'));
			
			$parser = &new RtfParser($listener);
		    $parser->parse('{\rtf1\ansi{\info{\title This is a Title}}Some {\i\big Italic }Text}');
			
			$this->assertFalse($parser->level);
			$listener->tally();
		}
	}
	
	class RtfListenerTest extends UnitTestCase {
		
		function testRawTitle() {
			$listener = &new RtfListener();
			$parser = &new RtfParser($listener);
			$parser->parse('{\rtf1\ansi{\info{\title This is a Title}}Some {\i\big Italic }Text}');
			$this->assertEqual($listener->getTitle(), 'This is a Title');
		}
		
		function testRequirementsDocumentTitle() {
			$listener = &new RtfListener();
			$parser = &new RtfParser($listener);
			$parser->parse(file_get_contents('samples/requirements.rtf'));
			$this->assertEqual($listener->getTitle(), 'Arbiter Project');
		}
		
		function testTitleOnlyDocumentTitle() {
			$listener = &new RtfListener();
			$parser = &new RtfParser($listener);
			$parser->parse(file_get_contents('samples/title_only.openoffice1.rtf'));
			$this->assertEqual($listener->getTitle(), 'Title Only');
		}
	}
?>