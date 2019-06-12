<?php declare(strict_types=1);

use hollodotme\FastCGI\Client;
use hollodotme\FastCGI\Requests\PostRequest;
use hollodotme\FastCGI\Responses\Response;

class FastCgiEventListener {

    /** @var Event[] */
    private $activeTasks = [];

    /** @var int */
    private $handledEvents = 0;

    /** @var int  */
    private $failedEvents = 0;

    /** @var Client */
    private $fastCgiClient;

    /** @var RedisEventStream */
    private $stream;

    /** @var CliOutput */
    private $output;

    public function __construct(Client $fastCgiClient, RedisEventStream $stream, CliOutput $output) {
        $this->fastCgiClient = $fastCgiClient;
        $this->stream = $stream;
        $this->output = $output;
    }

    public function listen(): void {
        while (true) {
            if ($this->fastCgiClient->hasUnhandledResponses()) {
                $this->fastCgiClient->handleReadyResponses();
            }
            $this->updateOutput();

            if (!$event = $this->stream->getNextEvent()) {
                usleep(15000);
                continue;
            }

            $request = new PostRequest('/app/handleEvent.php', $event->getPayload());
            $request->addResponseCallbacks([$this, 'handleResponse']);

            $requestId = $this->fastCgiClient->sendAsyncRequest($request);
            $this->activeTasks[$requestId] = $event;
            $this->updateOutput();
        }
    }

    public function handleResponse(Response $response): void {
        if ($response->getError() !== '') {
            $this->failedEvents++;
            unset($this->activeTasks[$response->getRequestId()]);
            return;
        }
        $this->handledEvents++;
        $this->stream->acknowledge($this->activeTasks[$response->getRequestId()]);
        unset($this->activeTasks[$response->getRequestId()]);
    }

    private function updateOutput(): void
    {
        $this->output->update(count($this->activeTasks), $this->handledEvents, $this->failedEvents);
    }
}
