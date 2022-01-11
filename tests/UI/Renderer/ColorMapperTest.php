<?php

namespace PhpCardGame\Game;

use PhpCardGame\Game\Card\Color;
use PhpCardGame\UI\Render\ColorMapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PhpCardGame\UI\Render\ColorMapper
 */
class ColorMapperTest extends TestCase
{
    private ColorMapper $colorMapper;

    public function setUp(): void
    {
        $this->colorMapper = new ColorMapper();
    }

    /**
     * @dataProvider expectedMappings
     */
    public function testColorsAreMappedToSymfonyConsoleColors(Color $color, string $expectedColor): void
    {
        $actualColor = $this->colorMapper->getDrawColor($color);
        $this->assertEquals($expectedColor, $actualColor);
    }

    /**
     * @return array<array<int,Color|string>>
     */
    public function expectedMappings(): array
    {
        return [
            [Color::Red, 'bright-red'],
            [Color::Green, 'green'],
            [Color::Blue, 'bright-blue'],
            [Color::Yellow, 'bright-yellow'],
        ];
    }
}
