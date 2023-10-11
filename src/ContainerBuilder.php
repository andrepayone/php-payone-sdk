<?php

declare(strict_types=1);

namespace Payone\Sdk;

use Payone\Sdk\Container\Container;
use Payone\Sdk\Container\ContainerException;

/**
 * Builds the container and applies SDK default dependencies.
 *
 * @author Fabian Böttcher <me@cakasim.de>
 * @since 0.1.0
 */
class ContainerBuilder
{
    /**
     * A list of all required bindings.
     */
    protected const REQUIRED_BINDINGS = [
        \Psr\Log\LoggerInterface::class,
        \Psr\Http\Message\UriFactoryInterface::class,
        \Psr\Http\Message\StreamFactoryInterface::class,
        \Psr\Http\Message\RequestFactoryInterface::class,
        \Psr\Http\Message\ResponseFactoryInterface::class,
        \Psr\Http\Message\ServerRequestFactoryInterface::class,
        \Psr\Http\Client\ClientInterface::class,
        \Payone\Sdk\Http\Service::class,
        \Payone\Sdk\Api\Service::class,
        \Payone\Sdk\Notification\Service::class,
        \Payone\Sdk\Redirect\Service::class,
        \Payone\Sdk\Config\ConfigInterface::class,
        \Payone\Sdk\Api\Format\EncoderInterface::class,
        \Payone\Sdk\Api\Format\DecoderInterface::class,
        \Payone\Sdk\Api\Client\ClientInterface::class,
        \Payone\Sdk\Notification\Processor\ProcessorInterface::class,
        \Payone\Sdk\Notification\Handler\HandlerManagerInterface::class,
        \Payone\Sdk\Redirect\Token\TokenFactoryInterface::class,
        \Payone\Sdk\Redirect\Token\Format\EncoderInterface::class,
        \Payone\Sdk\Redirect\Token\Format\DecoderInterface::class,
        \Payone\Sdk\Redirect\Token\Format\SignerInterface::class,
        \Payone\Sdk\Redirect\UrlGenerator\UrlGeneratorInterface::class,
        \Payone\Sdk\Redirect\Handler\HandlerManagerInterface::class,
        \Payone\Sdk\Redirect\Processor\ProcessorInterface::class,
    ];

    /**
     * The SDK service bindings.
     */
    protected const SERVICE_BINDINGS = [
        \Payone\Sdk\Http\Service::class,
        \Payone\Sdk\Api\Service::class,
        \Payone\Sdk\Notification\Service::class,
        \Payone\Sdk\Redirect\Service::class,
    ];

    /**
     * The default bindings of the SDK.
     */
    protected const DEFAULT_BINDINGS = [

        // --- PSR Bindings ---

        // PSR-7
        // Concrete PSR-7 implementation is provided by PSR-17
        // factory bindings below.

        // PSR-11
        // Container binds itself within the container constructor.

        // --- SDK Bindings ---

        // Config
        \Payone\Sdk\Config\ConfigInterface::class => [\Payone\Sdk\Config\Config::class, true],

        // API Format
        \Payone\Sdk\Api\Format\EncoderInterface::class => [\Payone\Sdk\Api\Format\Encoder::class, true],
        \Payone\Sdk\Api\Format\DecoderInterface::class => [\Payone\Sdk\Api\Format\Decoder::class, true],

        // API Client
        \Payone\Sdk\Api\Client\ClientInterface::class => [\Payone\Sdk\Api\Client\Client::class, true],

        // Notification
        \Payone\Sdk\Notification\Processor\ProcessorInterface::class    => [\Payone\Sdk\Notification\Processor\Processor::class, true],
        \Payone\Sdk\Notification\Handler\HandlerManagerInterface::class => [\Payone\Sdk\Notification\Handler\HandlerManager::class, true],

        // Redirect
        \Payone\Sdk\Redirect\Token\TokenFactoryInterface::class        => [\Payone\Sdk\Redirect\Token\TokenFactory::class, true],
        \Payone\Sdk\Redirect\Token\Format\EncoderInterface::class      => [\Payone\Sdk\Redirect\Token\Format\Encoder::class, true],
        \Payone\Sdk\Redirect\Token\Format\DecoderInterface::class      => [\Payone\Sdk\Redirect\Token\Format\Decoder::class, true],
        \Payone\Sdk\Redirect\Token\Format\SignerInterface::class       => [\Payone\Sdk\Redirect\Token\Format\Signer::class, true],
        \Payone\Sdk\Redirect\UrlGenerator\UrlGeneratorInterface::class => [\Payone\Sdk\Redirect\UrlGenerator\UrlGenerator::class, true],
        \Payone\Sdk\Redirect\Handler\HandlerManagerInterface::class    => [\Payone\Sdk\Redirect\Handler\HandlerManager::class, true],
        \Payone\Sdk\Redirect\Processor\ProcessorInterface::class       => [\Payone\Sdk\Redirect\Processor\Processor::class, true],
    ];

    /**
     * A list of external bindings.
     */
    protected const EXTERNAL_BINDINGS = [
        // PSR-17 bindings, from cakasim/payone-sdk-http-message package
        \Psr\Http\Message\UriFactoryInterface::class           => ['Cakasim\Payone\Sdk\Http\Factory\UriFactory', true],
        \Psr\Http\Message\StreamFactoryInterface::class        => ['Cakasim\Payone\Sdk\Http\Factory\StreamFactory', true],
        \Psr\Http\Message\RequestFactoryInterface::class       => ['Cakasim\Payone\Sdk\Http\Factory\RequestFactory', true],
        \Psr\Http\Message\ResponseFactoryInterface::class      => ['Cakasim\Payone\Sdk\Http\Factory\ResponseFactory', true],
        \Psr\Http\Message\ServerRequestFactoryInterface::class => ['Cakasim\Payone\Sdk\Http\Factory\ServerRequestFactory', true],

        // PSR-18 bindings, from cakasim/payone-sdk-stream-client package
        \Psr\Http\Client\ClientInterface::class => ['Cakasim\Payone\Sdk\Http\StreamClient\StreamClient', true],

        // PSR-3 bindings, from cakasim/payone-sdk-silent-logger package
        \Psr\Log\LoggerInterface::class => ['Cakasim\Payone\Sdk\Log\SilentLogger\SilentLogger', true],
    ];

    /**
     * @var Container The container.
     */
    protected $container;

    /**
     * Returns a list of bindings that are required for the SDk to work.
     *
     * @return array List of required bindings.
     */
    protected static function getRequiredBindings(): array
    {
        return static::REQUIRED_BINDINGS;
    }

    /**
     * Returns a list of SDK service bindings.
     *
     * @return array List of SDK service bindings.
     */
    protected static function getServiceBindings(): array
    {
        return static::SERVICE_BINDINGS;
    }

    /**
     * Returns a list of default bindings.
     *
     * @return array List of default bindings.
     */
    protected static function getDefaultBindings(): array
    {
        return static::DEFAULT_BINDINGS;
    }

    /**
     * Returns a list of external bindings.
     *
     * @return array List of external bindings.
     */
    protected static function getExternalBindings(): array
    {
        return static::EXTERNAL_BINDINGS;
    }

    /**
     * Constructs the ContainerBuilder.
     *
     * @throws ContainerException
     */
    final public function __construct()
    {
        $this->container = new Container();
        $this->bindServices();
    }

    /**
     * Returns the container.
     *
     * @return Container The container.
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Binds the SDK services.
     *
     * @throws ContainerException
     */
    protected function bindServices(): void
    {
        foreach (static::getServiceBindings() as $service) {
            $this->container->bind($service, null, true);
        }
    }

    /**
     * Binds default SDK dependencies.
     *
     * @throws ContainerException
     */
    protected function bindDefaults(): void
    {
        foreach (static::getDefaultBindings() as $id => $binding) {
            if (!$this->container->has($id)) {
                $this->container->bind($id, $binding[0], $binding[1]);
            }
        }
    }

    /**
     * Binds external SDK dependencies.
     *
     * @throws ContainerException
     */
    protected function bindExternal(): void
    {
        foreach (static::getExternalBindings() as $id => $binding) {
            if (!$this->container->has($id) && class_exists($binding[0])) {
                $this->container->bind($id, $binding[0], $binding[1]);
            }
        }
    }

    /**
     * Verifies that the container holds all required bindings.
     */
    protected function verifyRequiredBindings(): void
    {
        foreach (static::getRequiredBindings() as $id) {
            if (!$this->container->has($id)) {
                throw new \RuntimeException("The SDK container is missing the following dependency: '{$id}'.");
            }
        }
    }

    /**
     * Builds the container.
     *
     * @return Container The container.
     * @throws ContainerException
     */
    public function buildContainer(): Container
    {
        $this->bindDefaults();
        $this->bindExternal();
        $this->verifyRequiredBindings();
        return $this->container;
    }

    /**
     * Builds the default container with all SDK dependencies.
     *
     * @return Container The container.
     * @throws ContainerException
     */
    public static function buildDefaultContainer(): Container
    {
        return (new static())->buildContainer();
    }
}
