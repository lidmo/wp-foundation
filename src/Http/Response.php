<?php

namespace Lidmo\WP\Foundation\Http;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

class Response
{
    protected $response;

    public function __construct(ResponseInterface $response = null)
    {
        $this->response = $response ?? new GuzzleResponse();
    }

    public function response(): ResponseInterface
    {
        return $this->response;
    }

    public function json($data, $status = 200, array $headers = []): self
    {
        $body = json_encode($data);
        $response = $this->response->withHeader('Content-Type', 'application/json')
            ->withHeader('X-Powered-By', 'Lídmo')
            ->withHeader('X-Lidmo-Url', 'https://lidmo.com.br')
            ->withStatus($status)
            ->withBody(\GuzzleHttp\Psr7\stream_for($body));

        return new self($response);
    }

    public function status($status): self
    {
        $response = $this->response->withStatus($status);
        return new self($response);
    }

    public function header($header, $value): self
    {
        $response = $this->response->withHeader($header, $value);
        return new self($response);
    }
}