<?php

namespace spec\Codin\Relay;

use PhpSpec\ObjectBehavior;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class RelaySpec extends ObjectBehavior
{
    public function it_should_process_middleware(ServerRequestInterface $request, ResponseInterface $response, MiddlewareInterface $first, MiddlewareInterface $second)
    {
        $this->beConstructedWith([$first, $second]);
        $first->process($request, $this)->willReturn($response);
        $second->process($request, $this)->willReturn($response);
        $this->handle($request)->shouldReturn($response);
    }
}
