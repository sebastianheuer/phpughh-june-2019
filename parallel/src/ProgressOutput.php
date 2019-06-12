<?php declare(strict_types=1);

namespace ParallelPhp;
class ProgressOutput
{
    private $lines = 0;

    public function init(int $lines): void
    {
        $this->lines = $lines;
        echo "\033[2J";
        echo "\033[0;0H";
        for($i = 0; $i < $lines; $i++) {
            echo $this->getProgressBar(0, 100, '', 80) . "\n";
        }
    }

    public function update(int $line, int $progress): void
    {
        $this->jumpToLine($line);

        echo $this->getProgressBar($progress, 100, '', 80) . "\n";
    }

    public function finalize(): void
    {
        $this->jumpToLine($this->lines + 1);
        echo "All tasks finished\n";
    }

    public function markAsSucceeded(int $line): void
    {
        $this->jumpToLine($line);
        echo "\033[32m" . $this->getProgressBar(100, 100, '', 80). "\033[0m\n";
    }

    public function markAsFailed(int $line): void
    {
        $this->jumpToLine($line);
        echo "\033[91m" . $this->getProgressBar(0, 100, '', 80). "\033[0m\n";
    }

    private function jumpToLine(int $line): void
    {
        echo sprintf("\033[%d;0H", $line + 1);
        echo "\033[K";
    }

    private function getProgressBar($done, $total, $info="", $width=50) {
        $perc = round(($done * 100) / $total);
        $bar = round(($width * $perc) / 100);
        return sprintf("%3s%%[%s>%s]%s", (int)$perc, str_repeat("=", (int)$bar), str_repeat(" ", (int)($width-$bar)), $info);
    }
}
