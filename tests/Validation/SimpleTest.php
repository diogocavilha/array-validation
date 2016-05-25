<?php

use Validation\Simple;

class SimpleTest extends \PHPUnit_Framework_TestCase
{
    private $class;

    public function setUp()
    {
        $this->class = new Simple();
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegEx /\w+: A non empty array is expected./
     */
    public function itMustThrowInvalidArgumentExceptionInCaseRequriedFieldsParamIsNotValid()
    {
        $this->assertTrue(method_exists($this->class, 'setRequiredFields'), 'Method setRequiredFields must exist.');

        $this->class->setRequiredFields([]);
    }

    /**
     * @test
     */
    public function itCanSetRequiredFieldsWithFilters()
    {
        $testField = [
            'name' => FILTER_SANITIZE_STRING
        ];

        $this->assertTrue(method_exists($this->class, 'setRequiredFields'), 'Method setRequiredFields must exist.');
        $this->assertInstanceOf('\Validation\Simple', $this->class->setRequiredFields($testField), 'Not a fluent interface.');
    }

    /**
     * @test
     */
    public function itCanSetFieldsWithFilters()
    {
        $testField = [
            'name' => FILTER_SANITIZE_STRING
        ];

        $this->assertTrue(method_exists($this->class, 'setFields'), 'Method setFields must exist.');
        $this->assertInstanceOf('\Validation\Simple', $this->class->setFields($testField), 'Not a fluent interface.');
    }

    /**
     * @test
     */
    public function itCanValidateAnInputByItsFilters()
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
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegEx /Required params: \w+/
     */
    public function itMustThrowRuntimeExceptionWhenAnyRequiredFieldIsNotPresent()
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

    /**
     * @test
     */
    public function itCanValidateAnInputByItsFiltersAndOptionalFieldFilters()
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
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionRegEx /Invalid params: \w+/
     */
    public function itMustThrowInvalidArgumentExceptionWhenAnyOptionalFieldIsNotValid()
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

    /**
     * @test
     */
    public function itCanValidateOnlyOptionalFields()
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
     * @test
     * @expectedException RuntimeException
     */
    public function itMustThrowRuntimeExceptionWhenCallValidateMethodWithNeitherFieldsNorRequiredFields()
    {
        $this->assertTrue(method_exists($this->class, 'validate'), 'Method validate must exist.');

        $this->class->validate([
            'id' => 5,
        ]);
    }
}
