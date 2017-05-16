<?php

namespace Sylake\AkeneoProducerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SylakeAkeneoProducerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('old_sound_rabbit_mq')) {
            throw new \RuntimeException('Make sure OldSoundRabbitMqBundle is enabled.');
        }

        $container->prependExtensionConfig('old_sound_rabbit_mq', [
            'producers' => [
                'akeneo' => [
                    'exchange_options' => [
                        'name' => 'akeneo',
                        'type' => 'fanout',
                    ],
                ],
            ],
        ]);
    }
}
