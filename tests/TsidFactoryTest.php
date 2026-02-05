<?php

namespace Odan\Tsid\Test;

use DateTimeImmutable;
use DateTimeZone;
use Odan\Tsid\Tsid;
use Odan\Tsid\TsidFactory;
use PDO;
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
        $stack = [];

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

            $list[$number] = $i;
            $stack[] = $number;
        }

        // Check ordering 1
        $listSort = $list;
        ksort($listSort);
        $this->assertSame($listSort, $list, 'The list is not ordered');

        // Check ordering 2
        $listSort2 = array_flip($listSort);
        $this->assertSame($listSort2, $stack, 'The list is not ordered');
    }

    public function testSqlLite(): void
    {
        $pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT);');

        $statement = $pdo->prepare('INSERT INTO users (id, username) VALUES (?, ?)');

        $tsidFactory = new TsidFactory();

        $tsid = $tsidFactory->generate();
        $id = $tsid->toInt();
        $name = $tsid->toString();
        $statement->execute([$id, $name]);

        // Check last inserted id
        $newId = $pdo->lastInsertId();
        $this->assertSame((string)$id, $newId);

        // Fetch row by TSID
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id=:id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $expected = [
            'id' => $id,
            'username' => $name,
        ];
        $this->assertSame($expected, $user);
    }

    public function testCustomDateUtc(): void
    {
        $customEpoch = new DateTimeImmutable('2026-01-01T00:00:00.000Z', new DateTimeZone('UTC'));

        $factoryWithCustomEpoch = new TsidFactory(TsidFactory::NODE_BITS_1024, null, $customEpoch);
        $factoryWithDefaultEpoch = new TsidFactory(TsidFactory::NODE_BITS_1024);

        $tsidCustom = $factoryWithCustomEpoch->generate();
        $tsidDefault = $factoryWithDefaultEpoch->generate();

        // The custom epoch is later than the default epoch, so TSIDs generated
        // with the custom epoch will have a smaller time component (fewer milliseconds since epoch).
        // This means the custom epoch TSID should be smaller than the default epoch TSID.
        $this->assertLessThan($tsidDefault->toInt(), $tsidCustom->toInt());

        // Verify both TSIDs are valid 13-character strings
        $this->assertSame(13, strlen($tsidCustom->toString()));
        $this->assertSame(13, strlen($tsidDefault->toString()));
    }
}
