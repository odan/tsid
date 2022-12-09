<?php

namespace Odan\Tsid;

final class Tsid
{
    private int $number;

    /**
     * Base 32.
     * https://www.crockford.com/base32.html.
     *
     * @var array<int, string>
     */
    private array $alphabet = [
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
        7 => '7',
        8 => '8',
        9 => '9',
        10 => 'A',
        11 => 'B',
        12 => 'C',
        13 => 'D',
        14 => 'E',
        15 => 'F',
        16 => 'G',
        17 => 'H',
        18 => 'J',
        19 => 'K',
        20 => 'M',
        21 => 'N',
        22 => 'P',
        23 => 'Q',
        24 => 'R',
        25 => 'S',
        26 => 'T',
        27 => 'V',
        28 => 'W',
        29 => 'X',
        30 => 'Y',
        31 => 'Z',
    ];

    public function __construct(int $number)
    {
        $this->number = $number;
    }

    public function toInt(): int
    {
        return $this->number;
    }

    public function toString(): string
    {
        // 0b11111 = 31
        return sprintf(
            '%s%s%s%s%s%s%s%s%s%s%s%s%s',
            $this->alphabet[($this->number >> 60) & 0b11111],
            $this->alphabet[($this->number >> 55) & 0b11111],
            $this->alphabet[($this->number >> 50) & 0b11111],
            $this->alphabet[($this->number >> 45) & 0b11111],
            $this->alphabet[($this->number >> 40) & 0b11111],
            $this->alphabet[($this->number >> 35) & 0b11111],
            $this->alphabet[($this->number >> 30) & 0b11111],
            $this->alphabet[($this->number >> 25) & 0b11111],
            $this->alphabet[($this->number >> 20) & 0b11111],
            $this->alphabet[($this->number >> 15) & 0b11111],
            $this->alphabet[($this->number >> 10) & 0b11111],
            $this->alphabet[($this->number >> 5) & 0b11111],
            $this->alphabet[$this->number & 0b11111],
        );
    }

    public function equals(Tsid $other): bool
    {
        return $this->number === $other->number;
    }
}
