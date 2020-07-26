# Использование

```php
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Data\WebTemplateBucket;
use roxblnfk\SmartStream\SmartStreamFactory;

class Controller {
    private ContainerInterface $container;

    # При необходимости конвертировать произвольные данные в Response
    # Необязательный параметр $request может использоваться в поиске формата ответа
    protected function prepareResponse($data, ?ServerRequestInterface $request = null): ResponseInterface
    {
        if ($data instanceof ResponseInterface) {
            return $data;
        }
        if ($data instanceof StreamInterface) {
            $stream = $data;
        } else {
            $stream = $this->container->get(SmartStreamFactory::class)->createStream($data, $request);
        }
        return $this->container
            ->get(ResponseFactoryInterface::class)
            ->createResponse()
            ->withBody($stream);
    }

    # если DataBucket обрабатывается в коде вызова someAction
    public function someAction(SomeServie $service): DataBucket
    {
        # ApiBucket - пользовательское расширение DataBucket
        return (new ApiBucket())->withResult($service->doSomeAction());
    }

    # если DataBucket не обрабатывается в коде вызова pageMain и требуется получить ResponseInterface
    public function pageMain(SomeRepositiry $repository, ServerRequestInterface $request): ResponseInterface
    {
        return $this->prepareResponse((new WebTemplateBucket([
            'data' => $repository->getSomeData(),
        ]))->withLayout('main-layout')->withTemplate('some-template'), $request);
    }
}
```
