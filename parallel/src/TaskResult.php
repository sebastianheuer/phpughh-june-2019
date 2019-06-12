<?php declare(strict_types=1);

namespace ParallelPhp;
interface TaskResult
{
    public function getId(): int;

    public function succeeded(): bool;
}
