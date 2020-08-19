# Tiny PSR-15 Http Server Middleware Processor

Example

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

// Define some Middlewares
$response = new class() implements MiddlewareInterface {
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $psr17Factory = new Nyholm\Psr7\Factory\Psr17Factory();
        $responseBody = $psr17Factory->createStream('');
        return $psr17Factory->createResponse(200)->withBody($responseBody);
    }
};

$cors = new class() implements MiddlewareInterface {
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        return $handler->handle($request)->withHeader('Access-Control-Allow-Origin', '*');
    }
};

$exceptions = new class() implements MiddlewareInterface {
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (Throwable $exception) {
            $this->logger->error('Server Error', compact('exception'));
            $psr17Factory = new Nyholm\Psr7\Factory\Psr17Factory();
            $responseBody = $psr17Factory->createStream('Server Error');
            return $psr17Factory->createResponse(500)->withBody($responseBody);
        }
    }
};

// Create a request
$psr17Factory = new Nyholm\Psr7\Factory\Psr17Factory();
$request = $psr17Factory->createRequest('GET', 'http://tnyholm.se');

// Run middlewares on a request to create a response
$relay = new Codin\Relay([$cors, $exceptions, $response]);
$response = $relay->handle($request);
```
