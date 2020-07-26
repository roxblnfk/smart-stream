# Пакет SmartStream

## Конфигурация

Конфигурация контейнера

```php
use Psr\Container\ContainerInterface;
use roxblnfk\SmartStream\Converter\JSONConverter;
use roxblnfk\SmartStream\Converter\PrintRConverter;
use roxblnfk\SmartStream\Converter\XMLConverter;
use roxblnfk\SmartStream\ConverterMatcherInterface;
use roxblnfk\SmartStream\Data\WebTemplateBucket;
use roxblnfk\SmartStream\Matching\SimpleConverterMatcher;
use roxblnfk\SmartStream\Matching\SimpleMatcherConfig;

return [
    # Сервис подборки конвертера для DataBucket
    ConverterMatcherInterface::class => static fn (ContainerInterface $c) => $c->get(SimpleConverterMatcher::class),
    /** Конфигурация {@see SimpleConverterMatcher} */
    SimpleMatcherConfig::class => static function (ContainerInterface $container) {
        return (new SimpleMatcherConfig())
            # Пользовательские конвертеры и DataBucket'ы
            ->withFormat('html', MyWebViewConverter::class, 'text/html', [WebTemplateBucket::class])
            ->withFormat('twig', TwigConverter::class, 'text/html', [TwigDataBucket::class])

            ->withFormat('json', JSONConverter::class, 'application/xml')
            ->withFormat('xml', XMLConverter::class, 'application/xml')
            ->withFormat('debug', PrintRConverter::class, 'text/plain');
    },
];
```

## Использование

```php
use Psr\Container\ContainerInterface;use Psr\Http\Message\ResponseFactoryInterface;use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;use \roxblnfk\SmartStream\SmartStreamFactory;

class Controller {
    private ContainerInterface $container;
    /* ... */
    protected function prepareResponse($data): ResponseInterface
    {
        if ($data instanceof ResponseInterface) {
            return $data;
        }
        if ($data instanceof StreamInterface) {
            $stream = $data;
        } else {
            $stream = $this->container->get(SmartStreamFactory::class)->createStream($data);
        }
        return $this->container
            ->get(ResponseFactoryInterface::class)
            ->createResponse()
            ->withBody($stream);
    }
}
```

## Как это работает

При правильной интеграции пакета в ваш проект вы сможете возвращать из экшенов контроллера практически любые данные,
которые впоследствии будут автоматически помещены в соответствующий PSR-совместимый Stream и затем в Response.

Например, если экшен вернёт строку, \SplFileInfo или ресурс, то для создания PRS-Stream будет использована фабрика
StreamFactory пакета, который вы настроили в своём проекте качестве PSR-17. Если экшен вернёт генератор, то он будет
помещён в GeneratorStream. Любые другие данные будут помещены в сущность базового класса DataBucket, в затем в
BucketStream.
