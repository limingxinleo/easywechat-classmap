<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Fan\EasyWeChat;

use EasyWeChat\Kernel\HttpClient\RequestUtil;
use Hyperf\Context\Context;
use Symfony\Component\HttpClient\HttpClient as SymfonyClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class HttpClient implements HttpClientInterface
{
    public static array $hooks = [
        'request' => [],
    ];

    public function __construct(protected array $option = [])
    {
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $hooks = self::$hooks['request'] ?? [];
        foreach ($hooks as $hook) {
            if ($hook instanceof \Closure) {
                if (($res = $hook($method, $url, $options)) && $res instanceof HookResult) {
                    $method = $res->method;
                    $url = $res->url;
                    $options = $res->options;
                }
            }
        }

        return $this->client()->request($method, $url, $options);
    }

    public function stream(iterable|ResponseInterface $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->client()->stream($responses, $timeout);
    }

    public function withOptions(array $options): static
    {
        $this->option = array_replace($this->option, $options);
        $client = $this->client()->withOptions($options);
        Context::set($this->contextKey(), $client);
        return $this;
    }

    protected function client(): HttpClientInterface
    {
        return Context::getOrSet($this->contextKey(), function () {
            return SymfonyClient::create(RequestUtil::formatDefaultOptions($this->option));
        });
    }

    protected function contextKey(): string
    {
        return sprintf('%s:%s', static::class, $this->option['base_uri'] ?? 'default');
    }
}
