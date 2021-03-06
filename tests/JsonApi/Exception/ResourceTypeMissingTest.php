<?php
namespace WoohooLabsTest\Yin\JsonApi\Exception;

use PHPUnit_Framework_TestCase;
use WoohooLabs\Yin\JsonApi\Exception\ResourceTypeMissing;

class ResourceTypeMissingTest extends PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $exception = $this->createException();
        $this->assertEquals("A resource type must be included in the document!", $exception->getMessage());
    }

    private function createException()
    {
        return new ResourceTypeMissing();
    }
}
