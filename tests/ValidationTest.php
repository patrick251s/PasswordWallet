<?php

require_once ".././classes/Database.php";
require_once ".././classes/Validation.php";
use PHPUNIT\Framework\TestCase;

class ValidationTest extends TestCase {
    private $valid;
    
    protected function setUp(): void {
        $this->valid = new Validation();
    }

    /**
    * @dataProvider providerCorrectValuesForTestLoginValidation
    */
    public function testLoginValidation_returnsCorrectValues_correctParametersGiven($login) {
        $result = $this->valid->loginValidation($login);
        $this->assertTrue($result);
    }
    
    public function providerCorrectValuesForTestLoginValidation() {
        return [
            ['Patryk99'], ["pat9090"], ['mat6483mat'], ['pat'], ['qwerqwerqwerqwerqwerqwerqwerqwerqwerqwer'], [' Patryk99']
        ];
    }
    
    /**
    * @dataProvider providerIncorrectValuesForTestLoginValidation
    */
    public function testLoginValidation_returnsCorrectValues_incorrectParametersGiven($login) {
        $result = $this->valid->loginValidation($login);
        $this->assertFalse($result);
    }
    
    public function providerIncorrectValuesForTestLoginValidation() {
        return [
            ['pa'], ['qwerqwerqwerqwerqwerqwerqwerqwerqwerqwer1'], ['2login'], ['.login'], ['/login']
        ];
    }
    
    /**
    * @dataProvider providerCorrectValuesForTestMasterPasswordValidation
    */
    public function testMasterPasswordValidation_returnsCorrectValues_correctParametersGiven($pass, $pass2){
        $result = $this->valid->masterPasswordValidation($pass, $pass2);
        $this->assertTrue($result);
    }
    
    public function providerCorrectValuesForTestMasterPasswordValidation() {
        return [
            ['has', 'has'], ['password', 'password'], ['.3g*[/', '.3g*[/']
        ];
    }
    
    /**
    * @dataProvider providerIncorrectValuesForTestMasterPasswordValidation
    */
    public function testMasterPasswordValidation_returnsCorrectValues_incorrectParametersGiven($pass, $pass2){
        $result = $this->valid->masterPasswordValidation($pass, $pass2);
        $this->assertFalse($result);
    }
    
    public function providerIncorrectValuesForTestMasterPasswordValidation() {
        return [
            ['has', 'has123'], ['pa', 'pa'], [' fr', ' fr'], ['fe ', 'fe ']
        ];
    }
    
    /**
    * @dataProvider providerCorrectValuesForAddPasswordLoginValidation
    */
    public function testAddPasswordLoginValidation_returnsCorrrectValues_correctParametersGiven($login) {
        $result = $this->valid->addPasswordLoginValidation($login);
        $this->assertTrue($result);
    }
    
    public function providerCorrectValuesForAddPasswordLoginValidation() {
        return [
            ['Patryk'], ['Kazio'], ['Gavi55']
        ];
    }
    
    /**
    * @dataProvider providerIncorrectValuesForAddPasswordLoginValidation
    */
    public function testAddPasswordLoginValidation_returnsCorrrectValues_incorrectParametersGiven($login) {
        $result = $this->valid->addPasswordLoginValidation($login);
        $this->assertFalse($result);
    }
    
    public function providerIncorrectValuesForAddPasswordLoginValidation() {
        return [
            ['ds'], ['1'], ['']
        ];
    }
   
    public function testGetLoginNumber_returnsCorrectValues_correctParametesGiven() {
        $mock = $this->createMock(Database::class);
        $mock->method('getLoginNumber')->with('Patryk')->will($this->returnValue(1));
        $valid = new Validation($mock);
        $result = $valid->myGetLoginNumber("Patryk");
        $this->assertEquals(1, $result);
    }  
    
    /**
    * @dataProvider providerCorrectValuesForShowPasswordValidation
    */
    public function testShowPasswordValidation_correctParametrsGiven($userID, $passID) {
        $mock = $this->createMock(Database::class);
        $mock->method('getAllUserPassword')->with(1)->willReturn([1, 2, 3, 4, 33]);
        $valid = new Validation($mock);
        $result = $valid->showPasswordValidation($userID, $passID);
        $this->assertTrue($result);
    }  
    
    public function providerCorrectValuesForShowPasswordValidation() {
        return [
            [1, 1], [1, 3], [1, 33]
        ];
    }
    
    /**
    * @dataProvider providerIncorrectValuesForShowPasswordValidation
    */
    public function testShowPasswordValidation_incorrectParametrsGiven($userID, $passID) {
        $mock = $this->createMock(Database::class);
        $mock->method('getAllUserPassword')->with(1)->willReturn([1, 2, 3, 4, 33]);
        $valid = new Validation($mock);
        $result = $valid->showPasswordValidation($userID, $passID);
        $this->assertFalse($result);
    }  
    
    public function providerIncorrectValuesForShowPasswordValidation() {
        return [
            [1, 0], [1, 34], [1, 'gfg']
        ];
    } 
}