<?php declare(strict_types=1);

namespace ParallelPhp;
class TaskHandler
{
    public function handle(int $id, Task $task): TaskResult
    {
        $channel = \parallel\Channel::open((string)$id);

        $cycles = random_int(1, 200);
        for ($i = 0; $i < $cycles; $i++) {
            usleep(25000);
            $channel->send((int)round($i / $cycles * 100));
        }
        $channel->close();
        return new TaskSucceededResult($id);
    }
}
