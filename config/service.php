<?php

use Interop\Container\ContainerInterface;

use Comment\Handler;
use Comment\Service;
use Comment\Event;

use MongoDB\Client;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Phly\EventDispatcher\EventDispatcher;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

return [
    'factories' => [
        Handler\Index::class => function(ContainerInterface $container, $requestedName) {
            return new Handler\Index();
        },
        Handler\GetResources::class => function(ContainerInterface $container, $requestedName) {
            return (new Handler\GetResources())
                ->setResourceService($container->get(Service\Resource::class))
                ;
        },
        Handler\GetResource::class => function(ContainerInterface $container, $requestedName) {
            return (new Handler\GetResource())
                ->setResourceService($container->get(Service\Resource::class))
                ;
        },
        Handler\SetResource::class => function(ContainerInterface $container, $requestedName) {
            return (new Handler\SetResource())
                ->setResourceService($container->get(Service\Resource::class))
                ;
        },
        Handler\GetMessagesByResource::class => function(ContainerInterface $container, $requestedName) {
            return (new Handler\GetMessagesByResource())
                ->setMessageService($container->get(Service\Message::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Handler\GetMessage::class => function(ContainerInterface $container, $requestedName) {
            return (new Handler\GetMessage())
                ->setMessageService($container->get(Service\Message::class))
                ;
        },
        Handler\SetMessage::class => function(ContainerInterface $container, $requestedName) {
            return (new Handler\SetMessage())
                ->setMessageService($container->get(Service\Message::class))
                ;
        },

        Service\Resource::class => function(ContainerInterface $container, $requestedName) {
            return (new Service\Resource())
                ->setDriver($container->get(Service\DatabaseAware::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Service\Message::class => function(ContainerInterface $container, $requestedName) {
            return (new Service\Message())
                ->setDriver($container->get(Service\DatabaseAware::class))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },
        Service\DatabaseAware::class => function (ContainerInterface $container, $requestedName) {
            $db = getenv('DB_DATABASE') ? : 'comment';
            $host = getenv('DB_HOST') ? : 'localhost';
            $port = getenv('DB_PORT') ? : 27017;
            $user = getenv('DB_USER') ? rawurlencode(getenv('DB_USER')) : null;
            $pwd = getenv('DB_PASSWORD') ? rawurlencode(getenv('DB_PASSWORD')) : null;

            return (new MongoDB\Client(
                $user && $pwd
                    ? "mongodb://{$user}:{$pwd}@{$host}:{$port}/{$db}"
                    : "mongodb://{$host}:{$port}/{$db}"
            ))->selectDatabase($db);
        },

        EventDispatcherInterface::class => function (ContainerInterface $container, $requestedName) {
            $logger = $container->get(LoggerInterface::class);
            $provider = new AttachableListenerProvider();
            $provider->listen(Event\ServiceError::class, function (Event\ServiceError $event) use ($logger) : void {
                $logger->error((string) $event);
            });
            $provider->listen(Event\EntryView::class, function (Event\EntryView $event) use ($logger) : void {
                $logger->info((string) $event);
            });
            $provider->listen(Event\SystemError::class, function (Event\SystemError $event) use ($logger) : void {
                $logger->error((string) $event);
            });

            return new EventDispatcher($provider);
        },
        LoggerInterface::class => function (ContainerInterface $container, $requestedName) {
            $log = new Logger('comment-api');
            $log->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
            return $log;
        },
    ],
];
