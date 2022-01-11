<?php

namespace PhpCardGame\Game\Card;

enum Color {
    case Red;
    case Green;
    case Blue;
    case Yellow;

    static function random(): self
    {
        $key = array_rand(self::cases());
        return self::cases()[$key];
    }
}
