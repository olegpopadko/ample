<?php

namespace AppBundle\Tests\LogFile;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\LogFile;

class LineTest extends KernelTestCase
{
    public function testGetCreatedAt()
    {
        $line = '199.72.81.55 - - [01/Jul/1995:00:00:01 -0400] "GET /history/apollo/ HTTP/1.0" 200 6245';
        $logLine = new LogFile\Line($line);

        $this->assertEquals(new \DateTime('01/Jul/1995:00:00:01 -0400'), $logLine->getCreatedAt());
    }

    public function testGetCreatedAtException()
    {
        $line = 'string';
        $this->assertNull((new LogFile\Line($line))->getCreatedAt());
    }
}
