<?php declare(strict_types=1);

class RedisEventStream {
    /** @var array */
    private $eventMap = [];

    /** @var Redis */
    private $redis;

    /** @var string */
    private $streamIdentifier;

    /** @var string */
    private $consumerGroup;

    /** @var string */
    private $consumerName;

    public function __construct(
        Redis $redis,
        string $streamIdentifier,
        string $consumerGroup,
        string $consumerName
    ) {
        $this->redis = $redis;
        $this->streamIdentifier = $streamIdentifier;
        $this->consumerGroup = $consumerGroup;
        $this->consumerName = $consumerName;
    }

    public function acknowledge(Event $event): void {
        $this->redis->xAck(
            $this->streamIdentifier,
            $this->consumerGroup,
            [$this->eventMap[$event->getObjectHash()]]
        );
    }

    public function getNextEvent(): ?Event {
        $groupEvents = $this->redis->xReadGroup(
            $this->consumerGroup,
            $this->consumerName,
            [$this->streamIdentifier => '>'],
            1
        );
        if (!isset($groupEvents[$this->streamIdentifier])) {
            return null;
        }

        $redisEventId = key($groupEvents[$this->streamIdentifier]);
        $redisEventData = current($groupEvents[$this->streamIdentifier])['payload'];
        $event = Event::fromJson($redisEventData);
        $this->eventMap[$event->getObjectHash()] = $redisEventId;

        return $event;
    }
}
