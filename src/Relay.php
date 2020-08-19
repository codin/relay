<?php

declare(strict_types=1);

namespace Codin;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Relay implements RequestHandlerInterface
{
    /**
     * @var array<MiddlewareInterface>
     */
    protected $queue;

    /**
     * @var Closure
     */
    protected $resolver;

    public function __construct(array $queue, ?callable $resolver = null)
    {
        if (count($queue) === 0) {
            throw new RelayException('queue cannot be empty');
        }
        $this->queue = $queue;
        reset($this->queue);
        $this->resolver = is_callable($resolver) ?
            Closure::fromCallable($resolver) :
            static function (MiddlewareInterface $entry): MiddlewareInterface {
                return $entry;
            };
    }

    public function nextMiddleware(): MiddlewareInterface
    {
        $entry = current($this->queue);
        $middleware = ($this->resolver)($entry);
        next($this->queue);
        return $middleware;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->nextMiddleware()->process($request, $this);
    }
}
