# Пакет SmartStream

## Использование

```php
use Psr\Http\Message\ResponseInterface;
use \roxblnfk\SmartStream\SmartStreamFactory;

class Controller {
    private \Psr\Container\ContainerInterface $container;
    /* ... */
    function prepareResponse($data): ResponseInterface {
        if ($data instanceof ResponseInterface) {
            return $data;
        }
        if ($data instanceof \Psr\Http\Message\StreamInterface) {
            $stream = $data;
        } else {
            $stream = $this->container->get(SmartStreamFactory::class)->createStream($data);
        }
        return $this->container
            ->get(\Psr\Http\Message\ResponseFactoryInterface::class)
            ->createResponse()
            ->withBody($stream);
    }
}
```

```php
use roxblnfk\SmartStream\Converter\JSONConverter;
use roxblnfk\SmartStream\Converter\PrintRConverter;
use roxblnfk\SmartStream\Middleware\RenderDataStream;

$middleware = (new RenderDataStream($container))
    ->defineConverter(MyWebViewConverter::class, 'html', 'text/html', false)
    ->defineConverter(XMLConverter::class, 'xml', 'text/xml', false)
    ->defineConverter(JSONConverter::class, 'json', 'application/json', false)
    ->defineConverter(PrintRConverter::class, 'print_r', 'text/plain', false);
```

## Как это работает
