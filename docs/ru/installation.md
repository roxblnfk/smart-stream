# Установка

Предпочтительнее установить этот пакет через [composer](http://getcomposer.org/download/):

```
composer require roxblnfk/smart-stream
```

## Настройка

#### 1. Задайте определение `ConverterMatcherInterface` в контейнере

Если в качестве реализации `ConverterMatcherInterface` используется `SimpleConverterMatcher`,
то следует определить его настройки в объекте класса `SimpleMatcherConfig`.

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

#### 2. Добавьте BucketStreamMiddleware в пайплайн приложения

`BucketStreamMiddleware` разместите в стеке middleware перед роутером.

## Настройка SimpleConverterMatcher

Класс `SimpleConverterMatcher` производит поиск конвертера для `DataBucket` используя:

- Название формата, указанное в самом `DataBucket`.
- Настроенные связи между форматами и классами `DataBucket` с учётом их наследования.
- Параметры запроса клиента (HTTP-заголовок Accept).

Настройка `SimpleConverterMatcher` производится посредствам конфигурирования объекта `SimpleMatcherConfig`.
Для этого в `SimpleConverterMatcher` имеется единственный иммутабельный метод `withFormat()`.

```php
public function withFormat(string $format, string $converter, string $mimeType = null, array $buckets = []): self
```

* **format** - произвольное имя формата, которое может использоваться в `DataBucket` для однозначного присвоения
  формата данным.
* **converter** - соответствующий формату конвертер.
* **mimeType** - MIME-тип из запроса клиента, который может помочь сделать выбор в пользу этого формата при формировании
  ответа. Такое может произойти, например, если одному объекту `DataBucket` без явного указания формата соответствуют
  несколько возможных форматов (например, json и xml)
* **buckets** - указываются те `DataBucket`, которые могут быть преобразованы в указанный формат указанным конвертером.
  Т.о. `DataBucket` без явного указания формата будет сверяться с этим списком.
