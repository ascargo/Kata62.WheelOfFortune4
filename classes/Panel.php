<?php

class Panel
{
    public const MAX_CHARS_BY_LINE = 15;
    public const HIDDEN_CHAR = "_";

    private array $textToSolve;
    private array $hiddenCharsMap;

    public function __construct(private string $clue, string $text)
    {
        $this->textToSolve = [];
        $text = strtoupper($text);
        for ($i = 0; $i < strlen($text); ++$i) {
            $this->textToSolve[] = $text[$i];
            $this->hiddenCharsMap[] = true;
        }
    }
    public function show()
    {
        $currentCharIndex = 0;
        while ($currentCharIndex < count($this->textToSolve)) {
            if ($this->isHiddenChar($currentCharIndex)) {
                echo Panel::HIDDEN_CHAR;
            } else {
                echo $this->textToSolve[$currentCharIndex];
                $this->checkNewLine($currentCharIndex);
            }
            ++$currentCharIndex;
        }
        echo PHP_EOL.$this->clue.PHP_EOL;
    }

    public function isSolved(): bool
    {
        foreach ($this->textToSolve as $index => $charToSolve) {
            if ($charToSolve != " " && $this->hiddenCharsMap[$index]) {
                return false;
            }
        }
        return true;
    }

    public function trySolve(string $solution): bool
    {
        $solution = strtoupper(trim($solution));
        $text = implode($this->textToSolve);
        if ($solution === $text) {
            $this->revealAll();
            return true;
        }
        return false;
    }

    public function solveLetter(string $letter): int
    {
        $foundIndexs = array_keys($this->textToSolve, $letter, true);
        if (count($foundIndexs) > 0) {
            $showedLetters = $this->showLetters($foundIndexs);
            if ($showedLetters > 0) {
                return $showedLetters;
            }
        }
        return 0;
    }

    private function checkNewLine(int $charsNumber): void
    {
        if ($charsNumber >= self::MAX_CHARS_BY_LINE) {
            echo PHP_EOL;
        } else {
            echo " ";
        }
    }

    private function isHiddenChar(int $index): bool
    {
        return $this->textToSolve[$index] != " " && $this->hiddenCharsMap[$index];
    }

    private function showLetters(array $found_indexs): int
    {
        $showedLetters = 0;
        foreach ($found_indexs as $found_index) {
            if ($this->hiddenCharsMap[$found_index]) {
                $this->hiddenCharsMap[$found_index] = false;
                ++$showedLetters;
            }
        }
        return $showedLetters;
    }

    private function revealAll(): void
    {
        foreach ($this->hiddenCharsMap as $index => $_value) {
            $this->hiddenCharsMap[$index] = false;
        }
    }

}
