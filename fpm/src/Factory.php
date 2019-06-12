<?php declare(strict_types=1);

use hollodotme\FastCGI\SocketConnections\NetworkSocket;

class Factory {
    public function createEventListener(): FastCgiEventListener {
        return new FastCgiEventListener($this->createFastCgiClient(), $this->createEventStream(), new CliOutput());
    }

    private function createEventStream(): RedisEventStream {
        return new RedisEventStream(
            $this->createRedisClient(), 'events', 'service-a', 'some-consumer'
        );
    }

    private function createFastCgiClient(): hollodotme\FastCGI\Client {
        return new hollodotme\FastCGI\Client(
            new NetworkSocket('127.0.0.1', 9000)
        );
    }

    private function createRedisClient(): Redis {
        $redis = new Redis();
        $redis->connect('127.0.0.1');

        return $redis;
    }
}
