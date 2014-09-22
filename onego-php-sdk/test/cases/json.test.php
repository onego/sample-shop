<?php
define('ROOT_DIR', dirname(dirname(dirname(__FILE__))));
define('TESTS_DIR', dirname(dirname(__FILE__)));


require_once ROOT_DIR . '/simpletest/autorun.php';
require_once ROOT_DIR . '/src/OneGoSDK/init.php';

class SomeObject
{
    public $publicProperty = 1;
    private $protectedProperty = 2;
    
    public function dummyFunction() {}
}

class JSONTestCase extends UnitTestCase {
    function testJSONEncode() {
        $this->assertTrue(OneGoSDK_JSON::encode('aaa') == '"aaa"');
        $this->assertTrue(OneGoSDK_JSON::encode(123) == '123');
        $this->assertTrue(OneGoSDK_JSON::encode(true) == 'true');
        $this->assertTrue(OneGoSDK_JSON::encode(null) == 'null');
        $this->assertTrue(OneGoSDK_JSON::encode(array(1, 2, 3)) == '[1,2,3]');
        $this->assertTrue(OneGoSDK_JSON::encode(array()) == '[]');
        $this->assertTrue(OneGoSDK_JSON::encode(array(1, 2, 'aaa', 'key' => 'val')) == '{"0":1,"1":2,"2":"aaa","key":"val"}');
        $this->assertTrue(OneGoSDK_JSON::encode(new SomeObject()) == '{"publicProperty":1}');
    }
    
    function testJSONDecode() {
        $this->assertTrue(OneGoSDK_JSON::decode('"aaa"') === 'aaa');
        $this->assertTrue(OneGoSDK_JSON::decode('123') === 123);
        $this->assertTrue(OneGoSDK_JSON::decode('true') === true);
        $this->assertNull(OneGoSDK_JSON::decode('null'));
        $this->assertTrue(OneGoSDK_JSON::decode('[1,2,3]') === array(1, 2, 3));
        $this->assertTrue(OneGoSDK_JSON::decode('[]') === array());
        $decodedAssocArray = OneGoSDK_JSON::decode('{"0":1,"1":2,"2":"aaa","key":"val"}');
        $this->assertTrue(is_object($decodedAssocArray));
        $this->assertTrue($decodedAssocArray->key === 'val');
        $decodedObject = OneGoSDK_JSON::decode('{"publicProperty":1}');
        $this->assertTrue(is_object($decodedObject));
        $this->assertTrue($decodedObject->publicProperty === 1);
    }
}