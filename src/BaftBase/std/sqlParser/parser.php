<?php

class Parser {
	private $lexer;

	public function __construct($dql) {

		$this->lexer = new Lexer ();
		$this->lexer->setInput ( $dql );
	
	}
	
	// ...
	public function getAST() {
		// Parse & build AST
		$AST = $this->QueryLanguage ();
		
		// ...
		
		return $AST;
	
	}

	public function QueryLanguage() {

		$this->lexer->moveNext ();
		
		switch ($this->lexer->lookahead ['type']) {
			case Lexer::T_SELECT :
				$statement = $this->SelectStatement ();
				break;
			case Lexer::T_UPDATE :
				$statement = $this->UpdateStatement ();
				break;
			case Lexer::T_DELETE :
				$statement = $this->DeleteStatement ();
				break;
			default :
				$this->syntaxError ( 'SELECT, UPDATE or DELETE' );
				break;
		}
		
		// Check for end of string
		if ($this->lexer->lookahead !== null) {
			$this->syntaxError ( 'end of string' );
		}
		
		return $statement;
	
	}
	
	// ...
}

$parser = new Parser ( 'SELECT u FROM User u' );
$AST = $parser->getAST ();