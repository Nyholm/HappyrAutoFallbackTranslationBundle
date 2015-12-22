<?php

namespace Happyr\AutoFallbackTranslationBundle\Service;

use Http\Client\HttpClient;
use Http\Client\Plugin\CachePlugin;
use Http\Client\Plugin\LoggerPlugin;
use Http\Client\Plugin\PluginClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

abstract class TranslatorClient
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var CacheItemPoolInterface
     */
    private $cachePool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        if ($this->httpClient === null) {
            $plugins[] = new CachePlugin(
                $this->cachePool, StreamFactoryDiscovery::find(), [
                'respect_cache_headers' => false,
                'default_ttl' => 604800,
            ]
            );

            if ($this->logger !== null) {
                $plugins[] = new LoggerPlugin($this->logger);
            }

            $this->httpClient = new PluginClient(HttpClientDiscovery::find(), $plugins);
        }

        return $this->httpClient;
    }

    /**
     * Log something.
     *
     * @param string $level
     * @param string $message
     */
    protected function log($level, $message)
    {
        if ($this->logger !== null) {
            $this->logger->log($level, $message);
        }
    }

    /**
     * @param HttpClient $httpClient
     *
     * @return TranslatorClient
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * @param CacheItemPoolInterface $cachePool
     *
     * @return TranslatorClient
     */
    public function setCachePool(CacheItemPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;

        return $this;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return TranslatorClient
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
