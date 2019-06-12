<?php declare(strict_types=1);

class CliOutput
{
    public function update(int $numberOfTasks, int $numberOfFinishedEvents, int $numberOfFailedEvents):void {
        echo sprintf(
            " \033[32mHandled events: %d\033[0m | \e[91mFailed events: %d\e[0m | Active tasks: %d \r",
            $numberOfFinishedEvents,
            $numberOfFailedEvents,
            $numberOfTasks
        );
    }
}
