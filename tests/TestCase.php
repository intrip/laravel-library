<?php namespace Jacopo\Library\Tests;
/**
 * Test TestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class TestCase extends \Orchestra\Testbench\TestCase  {

    public function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            'Jacopo\Library\LibraryServiceProvider',
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
        ];
    }

    /**
     * @test
     **/
    public function dummy()
    {

    }
}