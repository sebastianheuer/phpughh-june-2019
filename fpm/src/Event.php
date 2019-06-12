<?php declare(strict_types=1);

class Event {
    /** @var string  */
    private $payload;

    public static function fromJson(string $jsonData): Event {
        return new self($jsonData);
    }

    public function __construct(string $payload) {
        $this->payload = $payload;
    }

    public function getPayload(): string {
        return $this->payload;
    }

    public function getObjectHash(): string {
        return spl_object_hash($this);
    }
}
