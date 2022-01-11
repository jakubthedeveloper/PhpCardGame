<?php

namespace PhpCardGame\UI\Render;

class HorizontalLinesMerger
{
    /**
     * @param array<array<string>> $lineSets
     * @return array<int, string>
     */
    public function mergeLines(array $lineSets): array
    {
        $mergedLines = [];
        foreach ($lineSets as $lineSet) {
            foreach (array_values($lineSet) as $key => $line) {
                if (false === array_key_exists($key, $mergedLines)) {
                    $mergedLines[$key] = '';
                }

                $mergedLines[$key] .= sprintf(' %s', $line);
                $mergedLines[$key] = trim($mergedLines[$key]);
            }
        }

        return $mergedLines;
    }
}
