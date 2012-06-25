<?php
// $Id: authentication_test.php,v 1.5 2005/03/02 12:44:39 maetl_ Exp $

class Authentication {
	function LoginAsClient() { }
	function LoginAsDeveloper() { }
	function logout() { }
	function isAllowed() { }
	function isClient() { }
	function isDeveloper() { }
}

Mock::generate('Authentication');

class BaseAuthenticationTest extends UnitTestCase {

	function setUp() {
		$this->Auth = &new MockAuthentication($this);
	}

    function testClientLogin() {
        $this->assertFalse($this->Auth->isClient());
        $this->Auth->LoginAsClient('mark');
        $this->assertTrue($this->Auth->isAllowed());
        $this->assertTrue($this->Auth->isClient());
        $this->Auth->Logout();
        $this->assertFalse($this->Auth->isClient());
    }

    function testDeveloperLogin() {
        $this->assertFalse($this->Auth->isDeveloper());
        $this->Auth->LoginAsDeveloper('mark');
        $this->assertTrue($this->Auth->isAllowed());
        $this->assertTrue($this->Auth->isDeveloper());
        $this->Auth->Logout();
        $this->assertFalse($this->Auth->isDeveloper());      
    }

}

?>