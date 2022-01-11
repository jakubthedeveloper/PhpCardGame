<?php

namespace PhpCardGame\UI\Render;

use PhpCardGame\Game\Card;

class CardRenderer
{
    const CARD_WIDTH = 18;

    private ColorMapper $colorMapper;

    public function __construct()
    {
        $this->colorMapper = new ColorMapper();
    }

    /**
     * @return string[]
     */
    public function getLines(Card $card, int $cardKey): array
    {
        $lines = [];

        $drawColor = $this->colorMapper->getDrawColor($card->color);

        $lines[] = $this->coloredLine(
            bgColor: $drawColor
        );

        $lines[] = $this->lineWithSidesBorder(
            message: sprintf("#%d %s", $cardKey, $card->color->name),
            borderColor: $drawColor,
            fgColor: $drawColor
        );

        $lines[] = $this->lineWithSidesBorder(
            message: 'Points: ' . $card->points(),
            borderColor: $drawColor
        );

        $lines[] = $this->lineWithSidesBorder(
            message: ' ',
            borderColor: $drawColor
        );

        $lines[] = $this->lineWithSidesBorder(
            message: 'Damage: ',
            borderColor: $drawColor
        );

        foreach ($card->powers as $power) {
            $lines[] = $this->cardPowerDamageLine(
                $power->colorToHit->name,
                $this->colorMapper->getDrawColor($power->colorToHit),
                $power->hitStrength,
                $drawColor
            );
        }

        $lines[] = $this->coloredLine(
            bgColor: $drawColor
        );

        return $lines;
    }

    /**
     * @return string[]
     */
    public function getCardSlotLines(): array
    {
        $lines = [];

        for ($i = 0; $i < 9; $i++) {
            $lines[] = $this->coloredLine("gray");
        }

        return $lines;
    }

    private function coloredLine(string $bgColor = 'default'): string
    {
        $message = str_pad(' ', self::CARD_WIDTH);

        return sprintf('<bg=%s>%s</>', $bgColor, $message);
    }

    private function lineWithSidesBorder(
        string       $message,
        string       $borderColor,
        string       $fgColor = 'default'
    ): string {
        $message = str_pad($message, self::CARD_WIDTH - 4);

        return sprintf('<bg=%s> </> <fg=%s>%s</> <bg=%s> </>', $borderColor, $fgColor, $message, $borderColor);
    }

    private function cardPowerDamageLine(
        string $damageColorName,
        string $damageDrawColor,
        int $value,
        string $borderColor
    ): string {
        $message = sprintf("  %s: %d", $damageColorName, $value);
        $message = str_pad($message, self::CARD_WIDTH - 4);

        $message = str_replace(
            sprintf("%s:", $damageColorName),
            sprintf("<fg=%s>%s:</>", $damageDrawColor, $damageColorName),
            $message
        );

        return sprintf('<bg=%s> </> %s <bg=%s> </>', $borderColor, $message, $borderColor);
    }
}
