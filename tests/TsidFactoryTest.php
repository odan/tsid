<?php

namespace Odan\Tsid\Test;

use Odan\Tsid\Tsid;
use Odan\Tsid\TsidFactory;
use PHPUnit\Framework\TestCase;

final class TsidFactoryTest extends TestCase
{
    public function testTsid(): void
    {
        $this->assertTrue(PHP_INT_MAX === 9223372036854775807);

        $tsid = new Tsid(388400145978465528);
        $this->assertSame('0ARYZVZXW377R', $tsid->toString());

        $tsidFactory = new TsidFactory(TsidFactory::NODE_BITS_1024);
        $loopMax = 1024 * 3;

        $list = [];
        for ($i = 0; $i < $loopMax; $i++) {
            $tsid = $tsidFactory->generate();
            $this->assertSame(13, strlen($tsid->toString()));
            $this->assertSame(18, strlen((string)$tsid->toInt()));
            $this->assertTrue($tsid->equals(clone $tsid));

            // 388836919842180769
            $number = $tsid->toInt();

            if (isset($list[$number])) {
                $this->fail('A duplicate TSID was created: ' . $number);
            }
            $list[$number] = 1;
            // echo $number . "\n";
        }

        $this->assertTrue(true);
    }
}
