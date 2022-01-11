<?php

namespace PhpCardGame\UI\Render;

use PhpCardGame\Game\Card;

class ColorMapper
{
    public function getDrawColor(Card\Color $color): string
    {
        return match ($color->name) {
            'Red' => 'bright-red',
            'Green' => 'green',
            'Blue' => 'bright-blue',
            'Yellow' => 'bright-yellow'
        };
    }
}
