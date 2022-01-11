<?php

namespace PhpCardGame\Game;

use PhpCardGame\Game\Card\Color;
use PhpCardGame\Game\Card\Power;
use PhpCardGame\UI\Render\CardRenderer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PhpCardGame\UI\Render\CardRenderer
 * @covers \PhpCardGame\Game\Card
 * @covers \PhpCardGame\Game\Card\Power
 * @covers \PhpCardGame\UI\Render\ColorMapper
 */
class CardRendererTest extends TestCase
{
    private CardRenderer $cardRenderer;

    public function setUp(): void
    {
        $this->cardRenderer = new CardRenderer();
    }

    public function testCardRendering(): void
    {
        $card = new Card(
            color: Color::Red,
            points: 15,
            powers: [
                new Power(Color::Yellow, 2),
                new Power(Color::Blue, 3),
                new Power(Color::Green, 4),
            ]
        );

        $expectedOutput = [
            '<bg=bright-red>                  </>',
            '<bg=bright-red> </> <fg=bright-red>#1 Red        </> <bg=bright-red> </>',
            '<bg=bright-red> </> <fg=default>Points: 15    </> <bg=bright-red> </>',
            '<bg=bright-red> </> <fg=default>              </> <bg=bright-red> </>',
            '<bg=bright-red> </> <fg=default>Damage:       </> <bg=bright-red> </>',
            '<bg=bright-red> </>   <fg=bright-yellow>Yellow:</> 2    <bg=bright-red> </>',
            '<bg=bright-red> </>   <fg=bright-blue>Blue:</> 3      <bg=bright-red> </>',
            '<bg=bright-red> </>   <fg=green>Green:</> 4     <bg=bright-red> </>',
            '<bg=bright-red>                  </>',
        ];

        $output = $this->cardRenderer->getLines($card, 1);

        $this->assertEquals($expectedOutput, $output);
    }

    public function testCardSlotRendering(): void
    {
        $expectedOutput = [
            '<bg=gray>                  </>',
            '<bg=gray>                  </>',
            '<bg=gray>                  </>',
            '<bg=gray>                  </>',
            '<bg=gray>                  </>',
            '<bg=gray>                  </>',
            '<bg=gray>                  </>',
            '<bg=gray>                  </>',
            '<bg=gray>                  </>',
        ];

        $output = $this->cardRenderer->getCardSlotLines();

        $this->assertEquals($expectedOutput, $output);
    }
}
