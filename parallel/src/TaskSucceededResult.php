<?php declare(strict_types=1);

namespace ParallelPhp;
class TaskSucceededResult implements TaskResult
{
    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {

        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function succeeded(): bool
    {
        return true;
    }

}
