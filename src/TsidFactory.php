<?php

namespace Odan\Tsid;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

final class TsidFactory implements TsidFactoryInterface
{
    public const NODE_BITS_256 = 8;

    public const NODE_BITS_1024 = 10;

    public const NODE_BITS_4096 = 12;

    // Used to preserve monotonicity when the system clock is
    // adjusted by NTP after a small clock drift or when the
    // system clock jumps back by 1 second due to leap second.
    public const CLOCK_DRIFT_TOLERANCE = 10_000;

    public const RANDOM_BITS = 22;

    public const RANDOM_MASK = 0x003FFFFF;

    private int $counter = 0;

    private int $lastTime;

    private int $node = 0;

    private int $nodeBits;

    private int $counterBits;

    private int $nodeMask;

    private int $counterMask;

    private int $customEpoch;

    public function __construct(int $nodeBits = self::NODE_BITS_1024, int $node = null)
    {
        $this->nodeBits = $nodeBits;

        // Number of milliseconds of 2020-01-01T00:00:00.000Z.
        $dateUtc = new DateTimeImmutable('2020-01-01T00:00:00.000Z', new DateTimeZone('UTC'));
        $this->customEpoch = (int)substr($dateUtc->format('Uu'), 0, 13);

        // setup constants that depend on node bits
        $this->counterBits = self::RANDOM_BITS - $this->nodeBits;
        $this->counterMask = self::RANDOM_MASK >> $this->nodeBits;
        $this->nodeMask = self::RANDOM_MASK >> $this->counterBits;

        // set up the node identifier
        $this->node = $this->getNode($node) & $this->nodeMask;

        // finally, initialize internal state
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $this->lastTime = (int)substr($now->format('Uu'), 0, 13);

        $this->counter = $this->getRandomCounter();
    }

    public function generate(): Tsid
    {
        // Time component (42 bits)
        // a number of milliseconds since 1970-01-01 (Unix epoch).
        $time = $this->getTime() << self::RANDOM_BITS;

        // Random component (22 bits):
        // a sequence of random bits generated by a secure random generator.
        $node = $this->node << $this->counterBits;
        $counter = $this->counter & $this->counterMask;

        return new Tsid($time | $node | $counter);
    }

    private function getTime(): int
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $time = (int)substr($now->format('Uu'), 0, 13);

        // Check if the current time is the same as the previous time or has moved
        // backwards after a small system clock adjustment or after a leap second.
        // Drift tolerance = (previous_time - 10s) < current_time <= previous_time
        if (($time > $this->lastTime - self::CLOCK_DRIFT_TOLERANCE) && ($time <= $this->lastTime)) {
            $this->counter++;
            // Carry is 1 if an overflow occurs after ++.
            $carry = $this->counter >> $this->counterBits;
            $this->counter = $this->counter & $this->counterMask;
            // increment time
            $time = $this->lastTime + $carry;
        } else {
            // If the system clock has advanced as expected,
            // simply reset the counter to a new random value.
            $this->counter = $this->getRandomCounter();
        }

        // save current time
        $this->lastTime = $time;

        // adjust to the custom epoch
        return $time - $this->customEpoch;
    }

    private function getNode(?int $node): int
    {
        // 2^22 - 1 = 4,194,303
        $mask = 0x3FFFFF;

        if ($node === null) {
            $node = random_int(PHP_INT_MIN, PHP_INT_MAX);
        }

        return $node & $mask;
    }

    /**
     * Returns a random counter value from 0 to 0x3fffff (2^22-1 = 4,194,303).
     *
     * The counter maximum value depends on the node identifier bits. For example,
     * if the node identifier has 10 bits, the counter has 12 bits.
     *
     * @throws Exception
     *
     * @return int A random number
     */
    private function getRandomCounter(): int
    {
        return random_int(PHP_INT_MIN, PHP_INT_MAX) & $this->counterMask;
    }
}
