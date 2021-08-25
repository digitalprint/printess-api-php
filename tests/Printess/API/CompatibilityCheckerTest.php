<?php
namespace Tests\Printess\Api;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Printess\Api\CompatibilityChecker;
use Printess\Api\Exceptions\IncompatiblePlatform;

class CompatibilityCheckerTest extends TestCase
{
    /**
     * @var CompatibilityChecker|MockObject
     */
    protected $checker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checker = $this->getMockBuilder(CompatibilityChecker::class)
            ->setMethods([
                "satisfiesPhpVersion",
                "satisfiesJsonExtension",
            ])
            ->getMock();
    }

    public function testCheckCompatibilityThrowsExceptionOnPhpVersion(): void
    {
        $this->expectException(IncompatiblePlatform::class);
        $this->checker->expects($this->once())
            ->method("satisfiesPhpVersion")
            ->willReturn(false); // Fail

        $this->checker->expects($this->never())
            ->method("satisfiesJsonExtension");

        $this->checker->checkCompatibility();
    }

    public function testCheckCompatibilityThrowsExceptionOnJsonExtension(): void
    {
        $this->expectException(IncompatiblePlatform::class);
        $this->checker->expects($this->once())
            ->method("satisfiesPhpVersion")
            ->willReturn(true);

        $this->checker->expects($this->once())
            ->method("satisfiesJsonExtension")
            ->willReturn(false); // Fail

        $this->checker->checkCompatibility();
    }
}
