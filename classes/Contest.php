<?php

include('classes/FortuneWheel.php');

class Contest
{
    public const CONTESTANTS_NUMBER = 3;
    public const THROW_WHEEL_MSG = " throw the wheel!";
    public const WHEEL_RESULT_MSG = "Result throw result was ";
    public const BANKRUPTCY_MSG = "OOOOOOOOOOOOOOOOOOOOOHHHHHH!!!";
    public const LOSE_TURN_MSG = "Oh...";
    public const ACTION_PROMPT = "Press L to guess a letter or S to solve the panel: ";
    public const WRONG_SOLUTION_MSG = "That was not the solution.";
    public const CORRECT_SOLUTION_MSG = "Correct! Panel solved.";

    public function __construct(private Panel $panel, private array $contestants)
    {
        $this->turnNumber = 0;
        $this->wheel = new FortuneWheel();
        $this->currentContestantIndex = 1;
    }

    public function play(): void
    {
        while (!$this->panel->isSolved()) {
            $passTurn = true;

            $this->panel->show();

            $this->currentContestantIndex = $this->turnNumber % self::CONTESTANTS_NUMBER;
            $currentContestant = $this->contestants[$this->currentContestantIndex];

            echo $currentContestant->getName().self::THROW_WHEEL_MSG.PHP_EOL;
            $wheelValue = $this->wheel->throw();
            echo self::WHEEL_RESULT_MSG.$wheelValue.PHP_EOL;

            if ($wheelValue == 'Bankruptcy') {
                $this->makeBankruptcy($currentContestant);
            } elseif ($wheelValue == 'Lose') {
                echo self::LOSE_TURN_MSG.PHP_EOL;
            } else {
                $passTurn = $this->playTurn($currentContestant, $wheelValue);
            }

            if ($passTurn) {
                ++$this->turnNumber;
            }
            $this->showScores();
        }
    }

    private function makeBankruptcy(Contestant $contestant): void
    {
        $contestant->declareBankruptcy();
        echo self::BANKRUPTCY_MSG.PHP_EOL;
    }

    private function playTurn(Contestant $contestant, int $wheelValue): bool
    {
        $action = $this->askAction();

        if ($action === 'S') {
            $solution = readline();
            echo PHP_EOL;
            if ($this->panel->trySolve($solution)) {
                echo self::CORRECT_SOLUTION_MSG.PHP_EOL;
                return false;
            }
            echo self::WRONG_SOLUTION_MSG.PHP_EOL;
            return true;
        }

        $currentLetter = $contestant->sayLetter();
        echo PHP_EOL;
        $solvedLetters = $this->panel->solveLetter($currentLetter);
        if ($solvedLetters > 0) {
            $contestant->updatePoints($solvedLetters * $wheelValue);
            return false;
        }
        return true;
    }

    private function askAction(): string
    {
        $valid = ['L','S'];
        while (true) {
            echo self::ACTION_PROMPT;
            $input = strtoupper(trim(readline()));
            if (in_array($input, $valid, true)) {
                return $input;
            }
        }
    }

    private function showScores(): void
    {
        foreach ($this->contestants as $contestant) {
            echo $contestant->getName()." : ".$contestant->getScore().PHP_EOL;
        }
    }
}
