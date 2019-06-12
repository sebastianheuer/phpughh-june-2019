<?php declare(strict_types=1);

class EventHandler {
    public function handle(string $payload): string {
        sleep(random_int(0, 2));

        if(random_int(1, 10) === 9) {
            throw new RuntimeException('FAILED');
        }

        return $payload;
    }
}
