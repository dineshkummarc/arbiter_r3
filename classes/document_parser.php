<?php

    /**
	 * handler for rtf document parser
	 */
	class RtfListener {
	
		function RtfListener() {
			$this->mode = 'raw';
			$this->fragments = '';
			$this->paragraphs = array();
		}
	
		function startElement($token, $level) {
			$this->mode = $token;
			if ($this->mode == 'par') {
				$this->paragraphs[] = $this->fragments;
				$this->fragments = '';
			}
		}
		
		function addContent($token, $level) {
			if ($this->mode == 'title') {
				$this->title = $token;
				$this->mode = 'raw';
			} elseif ($this->mode == 'pard') {
				$this->fragments .= $token;
			}
		}

		function endElement($token) {
		}
		
		function addProperty($token) {
		}
		
		function addStyle($token) {
		}
		
		/**
		 * returns the document title
		 */
		function getTitle() {
			if (isset($this->title)) {
				return $this->title;
			} else {
				if (isset($this->paragraphs[0])) {
					return trim($this->paragraphs[0]);
				} else {
					return 'Untitled Document';
				}
			}
		}
	}

    define('PARSER_GROUP_START', 1);
	define('PARSER_GROUP_PROPERTY', 2);
	define('PARSER_GROUP_TEXT', 3);

    /**
     * Converts RTF Tokens into specified events
	 * @todo state stack
     */
    class RtfParser {
	    var $listener;
		var $level;
		var $scope;

		/**
		 * Initializes the listener and scanner
		 */
	    function RtfParser(&$listener) {
		    $this->listener = &$listener;
			$this->level = 0;
			$this->state = 0;
			$this->scope = array();
	    }

	    /**
	     * Parses the raw document and emits token events
	     */
	    function parse($raw) {
		    $scanner = &new RtfScanner($this);
		    $scanner->tokenize($raw);
	    }

        /**
		 * accepts a group start
		 */
		function acceptStartGroup() {
			$this->level++;
			$this->state = PARSER_GROUP_START;
		}
		
		/**
		 * accepts a group ending
		 */
		function acceptEndGroup() {
			$this->level--;
			$this->listener->endElement(array_pop($this->scope));
		}

		/**
		 * accepts a control word
		 */
        function acceptControl($token) {
			if ($this->isWantedElement($token)) {
				if ($this->state == PARSER_GROUP_START) {
					array_push($this->scope, $token);
					$this->state = PARSER_GROUP_PROPERTY;
					$this->listener->startElement($token, $this->level);
				} else {
					if ($this->state == PARSER_GROUP_PROPERTY) {
						if (isset($this->scope[0])) {
							$this->listener->addProperty($token, $this->scope[0]);
						}
					}
				}
			}
		}
		
		/**
		 * acceptor for character data stream
		 */
		function acceptContent($token) {
			$this->listener->addContent($token, $this->level);
		}
		
		/**
		 * checks if the token is a useful element
		 */
		 function isWantedElement($token) {
		 	return (in_array($token, array('rtf1', 'info', 'title', 'i', 'pard', 'par')));
		 }
		
		/**
		 * acceptor for a bad input stream
		 */
		function badInput($location) {
			trigger_error("Parser failed on bad input at char: [$location]");
		}
		
    }

    /**
	 * Character scanner looks for RTF control words,
	 * and emits all other characters as content events
     */
    class RtfScanner {
		var $parser;
        var $source;
        var $length;
        var $position;
	
        /**
         * @param  string  raw document text to parse
	     * @param  parser  event handler to read the document
         */
        function RtfScanner(&$parser) {
	        $this->parser = &$parser;
        }
		
        /**
		 * checks for control delimiter
		 */
        function isControlBoundary($char) {
            return (($char == ' ') || ($char == '\\') || ($char == '{') || ($char == '}'));
        }

        /**
		 * checks for text delimiter
		 */
	    function isTextBoundary($char) {
		    return (($char == '\\') || ($char == '{') || ($char == '}'));
	    }

        /**
		 * dispatches a control word token
		 */
		function emitControl() {
            if ($this->position >= $this->length) {
                return false;
            }
            $lookAhead = true;
		    $this->position++;
	        $token = '';
            while($lookAhead) {
                $char = $this->source{$this->position};
		        if ($char != '\\') {
		            $token .= $char;
                }
		        if ($this->position+1 < $this->length) {
                    $nextChar = $this->source{$this->position + 1};
                    if ($this->isControlBoundary($nextChar)) {
			            $lookAhead = false;
                    }
                } else {
                    $this->parser->badInput($this->position);
			        $lookAhead = false;
                }
			    if ($lookAhead) $this->position++;
            }
	        $this->parser->acceptControl($token);
        }
		
	    /**
	     * dispatches a text token
	     */
	    function emitContent() {
            if ($this->position >= $this->length) {
                return false;
            }
            $lookAhead = true;
	        $token  = '';
            while($lookAhead) {
                $char = $this->source{$this->position};
                $token.= $char;
                if ($this->position+1 < $this->length) {
                    $nextChar = $this->source{$this->position + 1};
                    $lookAhead = (!$this->isTextBoundary($nextChar));
                } else {
                    $lookAhead = false;
                }
                if ($lookAhead) $this->position++;
            }
	        $this->parser->acceptContent($token);
        }
		
		/**
		 * dispatches a group start
		 */
		function emitStartGroup() {
			$this->parser->acceptStartGroup();
		}
		
		/**
		 * dispatches a group ending
		 */
		function emitEndGroup() {
			$this->parser->acceptEndGroup();
		}

	    /**
		 * main parser loop
		 */
		function tokenize($raw) {
		    $this->source = $raw;
			$this->length = strlen($this->source);
            $this->position = 0;			
			while($this->position < $this->length) {
                $char = $this->source{$this->position};
		    	switch($char) {
				    case '{':
						$this->emitStartGroup();
                    case '\\':
						$this->emitControl();
						$this->position++;
						break;
					case '}':
						$this->emitEndGroup();
					case ' ':
						$this->position++;
						break;
					default:
						$this->emitContent();
						$this->position++;
						break;
				}
			}
		}
	}
?>