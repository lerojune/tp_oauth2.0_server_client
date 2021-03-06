<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Configuration\Option;

use Predis\Configuration\OptionInterface;
use Predis\Configuration\OptionsInterface;
use Predis\Connection\Factory;
use Predis\Connection\FactoryInterface;

/**
 * Configures a connection factory used by the client to create new connection
 * instances for single Redis nodes.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class Connections implements OptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(OptionsInterface $options, $value)
    {
        if (is_callable($value)) {
            $value = call_user_func($value, $options);
        }

        if ($value instanceof FactoryInterface) {
            return $value;
        } elseif (is_array($value)) {
            $factory = $this->getDefault($options);

            foreach ($value as $scheme => $initializer) {
                $factory->define($scheme, $initializer);
            }

            return $factory;
        } else {
            $class = get_called_class();

            throw new \InvalidArgumentException("$class expects a valid connection factory");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(OptionsInterface $options)
    {
        $factory = new Factory();

        if ($options->defined('parameters')) {
            $factory->setDefaultParameters($options->parameters);
        }

        return $factory;
    }
}
