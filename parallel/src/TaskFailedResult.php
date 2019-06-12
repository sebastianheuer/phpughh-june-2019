<?php declare(strict_types=1);

namespace ParallelPhp;
class TaskFailedResult implements TaskResult
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $errorMessage;

    public function __construct(int $id, string $errorMessage)
    {
        $this->id = $id;
        $this->errorMessage = $errorMessage;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function succeeded(): bool
    {
        return false;
    }
}
