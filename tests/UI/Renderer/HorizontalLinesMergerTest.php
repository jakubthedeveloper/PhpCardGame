<?php

namespace PhpCardGame\Game;

use PhpCardGame\UI\Render\HorizontalLinesMerger;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PhpCardGame\UI\Render\HorizontalLinesMerger
 */
class HorizontalLinesMergerTest extends TestCase
{
    private HorizontalLinesMerger $horizontalLinesMerger;

    public function setUp(): void
    {
        $this->horizontalLinesMerger = new HorizontalLinesMerger();
    }

    public function testLinesFromMultipleArraysAreMerged(): void
    {
        $input = [
            [
                'First item first line.',
                'First item second line.',
                'First item third line.',
                'First item fourth line.',
            ],
            [
                'Second item first line.',
                'Second item second line.',
                'Second item third line.',
                'Second item fourth line.',
            ],
            [
                'Third item first line.',
                'Third item second line.',
                'Third item third line.',
                'Third item fourth line.',
            ],
        ];

        $expectedOutput = [
            "First item first line. Second item first line. Third item first line.",
            "First item second line. Second item second line. Third item second line.",
            "First item third line. Second item third line. Third item third line.",
            "First item fourth line. Second item fourth line. Third item fourth line.",
        ];

        $output = $this->horizontalLinesMerger->mergeLines($input);

        $this->assertEquals($expectedOutput, $output);
    }
}
