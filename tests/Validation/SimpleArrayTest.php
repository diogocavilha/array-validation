<?php

use Validation\SimpleArray;

class SimpleArrayTest extends \PHPUnit_Framework_TestCase
{
    private $class;

    public function setUp()
    {
        $this->class = new SimpleArray();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegEx /\w+: A non empty array is expected./
     */
    public function testItMustThrowInvalidArgumentExceptionInCaseRequriedFieldsParamIsNotValid()
    {
        $this->assertTrue(method_exists($this->class, 'setRequiredFields'), 'Method setRequiredFields must exist.');

        $this->class->setRequiredFields([]);
    }

    public function testItCanSetRequiredFieldsWithFilters()
    {
        $testField = [
            'name' => FILTER_SANITIZE_STRING
        ];

        $this->assertTrue(method_exists($this->class, 'setRequiredFields'), 'Method setRequiredFields must exist.');
        $this->assertInstanceOf('\Validation\SimpleArray', $this->class->setRequiredFields($testField), 'Not a fluent interface.');
    }

    public function testItCanSetFieldsWithFilters()
    {
        $testField = [
            'name' => FILTER_SANITIZE_STRING
        ];

        $this->assertTrue(method_exists($this->class, 'setFields'), 'Method setFields must exist.');
        $this->assertInstanceOf('\Validation\SimpleArray', $this->class->setFields($testField), 'Not a fluent interface.');
    }

    public function testItCanValidateAnInputByItsFilters()
    {
        $this->assertTrue(method_exists($this->class, 'validate'), 'Method validate must exist.');

        $this->class->setRequiredFields([
            'name' => FILTER_SANITIZE_STRING,
            'age' => FILTER_VALIDATE_INT,
        ]);

        $this->class->validate([
            'name' => 'Diogo',
            'age' => 26
        ]);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegEx /Required params: \w+/
     */
    public function testItMustThrowRuntimeExceptionWhenAnyRequiredFieldIsNotPresent()
    {
        $this->assertTrue(method_exists($this->class, 'validate'), 'Method validate must exist.');

        $this->class->setRequiredFields([
            'name' => FILTER_SANITIZE_STRING,
            'age' => FILTER_VALIDATE_INT,
        ]);

        $this->class->validate([
            'name' => 'Diogo'
        ]);
    }

    public function testItCanValidateAnInputByItsFiltersAndOptionalFieldFilters()
    {
        $this->assertTrue(method_exists($this->class, 'validate'), 'Method validate must exist.');

        $this->class->setRequiredFields([
            'name' => FILTER_SANITIZE_STRING,
            'age' => FILTER_VALIDATE_INT,
        ]);

        $this->class->setFields([
            'id' => FILTER_VALIDATE_INT,
        ]);

        $this->class->validate([
            'name' => 'Diogo',
            'age' => 26,
            'id' => '50',
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionRegExp /Invalid params: \w+/
     */
    public function testItMustThrowInvalidArgumentExceptionWhenAnyOptionalFieldIsNotValid()
    {
        $this->assertTrue(method_exists($this->class, 'validate'), 'Method validate must exist.');

        $this->class->setRequiredFields([
            'name' => FILTER_SANITIZE_STRING,
            'age' => FILTER_VALIDATE_INT,
        ]);

        $this->class->setFields([
            'id' => FILTER_VALIDATE_INT,
        ]);

        $this->class->validate([
            'name' => 'Diogo',
            'age' => 26,
            'id' => 'test',
        ]);
    }

    public function testItCanValidateOnlyOptionalFields()
    {
        $this->assertTrue(method_exists($this->class, 'validate'), 'Method validate must exist.');

        $this->class->setFields([
            'id' => FILTER_VALIDATE_INT,
        ]);

        $this->class->validate([
            'id' => '5',
        ]);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testItMustThrowRuntimeExceptionWhenCallValidateMethodWithNeitherFieldsNorRequiredFields()
    {
        $this->assertTrue(method_exists($this->class, 'validate'), 'Method validate must exist.');

        $this->class->validate([
            'id' => 5,
        ]);
    }

    public function testItCanReturnTheValidArray()
    {
        $this->assertTrue(method_exists($this->class, 'getValidArray'), 'Method getValidArray must exist');

        $input = [
            'ignored' => 'This field must be ignored',
            'ignored_too' => 'This field must be ignored too',
            'id' => '55a',
            'name' => '<strong>Diogo</strong>',
            'description' => "<b>This is a test</b>, to know more about it <a href='index.phtml'>click here</a>"
        ];

        $rules = [
            'id' => FILTER_SANITIZE_NUMBER_INT,
            'name' => FILTER_SANITIZE_STRING,
            'description' => FILTER_SANITIZE_STRING
        ];

        $validArray = $this->class
            ->setFields($rules)
            ->validate($input)
            ->getValidArray();

        $this->assertInternalType('array', $validArray, 'It must be an array');
        $this->assertEquals('55', $validArray['id'], 'It must be 55');
        $this->assertEquals('Diogo', $validArray['name'], 'It must be Diogo');
        $this->assertEquals($description = 'This is a test, to know more about it click here', $validArray['description'], 'It must be ' . $description);
    }

    public function testItMustRemoveAllFieldsThatAreNotPresentInRules()
    {
        $input = [
            'ignored' => 'This field must be ignored',
            'ignored_too' => 'This field must be ignored too',
            'id' => '55test',
            'name' => '<strong>Diogo</strong>',
            'description' => "<b>This is a test</b>, to know more about it <a href='index.phtml'>click here</a>"
        ];

        $rules = [
            'id' => FILTER_SANITIZE_NUMBER_INT,
            'name' => FILTER_SANITIZE_STRING,
            'description' => FILTER_SANITIZE_STRING
        ];

        $validArray = $this->class
            ->setFields($rules)
            ->validate($input)
            ->getValidArray();

        $this->assertCount(3, $validArray);
        $this->assertArrayHasKey('id', $validArray, 'Key id must exist');
        $this->assertArrayHasKey('name', $validArray, 'Key name must exist');
        $this->assertArrayHasKey('description', $validArray, 'Key description must exist');
        $this->assertEquals(55, $validArray['id'], 'id must be 55');
        $this->assertEquals('Diogo', $validArray['name'], 'name must be Diogo');

        $specDescription = 'This is a test, to know more about it click here';
        $this->assertEquals($specDescription, $validArray['description'], 'description must be ' . $specDescription);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Cannot remove the field "\w+". This field is about to be validated./
     */
    public function testItMustThrowRuntimeExceptionWhenTryingToRemoveAFieldThatIsInSomeRule()
    {
        $this->assertTrue(method_exists($this->class, 'removeOnly'), 'Method removeOnly must exist.');

        $input = [
            'id' => '55test',
            'name' => '<strong>Diogo</strong>',
            'description' => "<b>This is a test</b>, to know more about it <a href='index.phtml'>click here</a>"
        ];

        $rules = [
            'id' => FILTER_SANITIZE_NUMBER_INT,
            'name' => FILTER_SANITIZE_STRING,
            'description' => FILTER_SANITIZE_STRING
        ];

        $fieldsToRemove = [
            'id',
        ];

        $this->class
            ->setFields($rules)
            ->removeOnly($fieldsToRemove)
            ->validate($input);
    }

    /**
     * @dataProvider fieldsProvider
     */
    public function testItCanRemoveOnlySomeFieldsFromInputArray($fieldsToRemove, $fieldToRemove)
    {
        $input = [
            'id' => '55test',
            'name' => '<strong>Diogo</strong>',
            'description' => "<b>This is a test</b>, to know more about it <a href='index.phtml'>click here</a>",
            'email' => 'email@domain.com',
            'phone' => '5555555',
        ];

        $rules = [
            'id' => FILTER_SANITIZE_NUMBER_INT,
            'name' => FILTER_SANITIZE_STRING,
            'description' => FILTER_SANITIZE_STRING
        ];

        $this->class
            ->setFields($rules)
            ->removeOnly($fieldsToRemove)
            ->validate($input);

        $data = $this->class->getValidArray();

        $this->assertArrayNotHasKey($fieldToRemove, $data);
    }

    public function fieldsProvider()
    {
        return [
            [['email'], 'email'],
            [['phone'], 'phone'],
        ];
    }
}
