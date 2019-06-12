<?php declare(strict_types=1);

namespace ParallelPhp;

class CalculateCommand
{
    /**
     * @var ProgressOutput
     */
    private $progressOutput;

    /**
     * @var \parallel\Events
     */
    private $events;

    /**
     * @var array \parallel\Runtime[]
     */
    private $runtimes = [];

    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    public function __construct(ProgressOutput $progressOutput)
    {
        $this->progressOutput = $progressOutput;
    }

    public function execute(array $tasks): void
    {
        $this->progressOutput->init(count($tasks));

        $this->events = new \parallel\Events();
        $this->events->setTimeout(1);

        $this->loop = \React\EventLoop\Factory::create();
        $this->catchSigintSignal();
        $this->exitWhenNoRuntimesAreLeft();
        $this->checkForEventsPeriodically();

        $this->registerRuntimes($tasks);

        $this->loop->run();

        $this->progressOutput->finalize();
    }

    private function registerRuntimes(array $tasks): void
    {
        foreach ($tasks as $id => $task) {

            $this->events->addChannel(\parallel\Channel::make((string)$id, \parallel\Channel::Infinite));

            $runtime = new \parallel\Runtime(__DIR__ . '/../vendor/autoload.php');
            $future = $runtime->run(function (int $id, string $serializedTask) {
                $taskHandler = new TaskHandler();
                $task = unserialize($serializedTask);
                return $taskHandler->handle($id, $task);
            }, [$id, serialize($task)]);

            $this->events->addFuture('task'.(string)$id, $future);
            $this->runtimes[$id] = $runtime;

            unset($tasks[$id]);
        }
    }

    private function checkForEventsPeriodically(): void
    {
        $this->loop->addPeriodicTimer(0.001, function () use (&$timer) {
            try {
                while ($event = $this->events->poll()) {
                    $this->handleEvent($event);
                }
            } catch (\parallel\Events\Error\Timeout $exception) {
                return;
            }
        });
    }

    private function handleEvent(\parallel\Events\Event $event) {
        if ($event->type !== \parallel\Events\Event\Type::Read) {
            return;
        }
        if ($event->object instanceof \parallel\Channel) {
            $id = (int)$event->source;
            if(!array_key_exists($id, $this->runtimes)) {
                return;
            }
            try {
                $progress = (int)$event->object->recv();
                $this->progressOutput->update($id, $progress);
                $this->events->addChannel($event->object);
            } catch (\parallel\Channel\Error\Closed $exception) {
            }
            return;
        }
        if ($event->object instanceof \parallel\Future) {
            $result = $event->value;
            /** @var TaskResult $result */
            $id = $result->getId();
            if (!$result->succeeded()) {
                $this->progressOutput->markAsFailed($id);
                unset($this->runtimes[$id]);
                return;
            }
            $this->progressOutput->markAsSucceeded($id);
            unset($this->runtimes[$id]);
        }
    }

    private function exitWhenNoRuntimesAreLeft(): void
    {
        $this->loop->addPeriodicTimer(0.05, function () use (&$timer) {
            if (count($this->runtimes) === 0) {
                $this->progressOutput->finalize();
                exit;
            }
        });
    }

    private function catchSigintSignal(): void
    {
        $this->loop->addSignal(SIGINT, function() {
            echo "SIGINT received \n";
            foreach ($this->runtimes as $runtime) {
                $runtime->close();
            }
            $this->loop->stop();
            exit;
        });
    }
}
