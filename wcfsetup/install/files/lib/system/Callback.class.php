<?php

namespace wcf\system;

use wcf\system\exception\SystemException;

/**
 * Represents a callback
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @deprecated  since 3.0, use callables and `callable` type hint directly
 */
final class Callback
{
    /**
     * encapsulated callback
     * @var callable
     */
    private $callback;

    /**
     * Creates new instance of Callback.
     *
     * @param callable $callback
     * @throws  SystemException
     */
    public function __construct($callback)
    {
        if (!\is_callable($callback)) {
            throw new SystemException('Given callback is not callable.');
        }

        $this->callback = $callback;
    }

    /**
     * Invokes our callback. All parameters are simply passed through.
     *
     * @return  mixed
     */
    public function __invoke()
    {
        return \call_user_func_array($this->callback, \func_get_args());
    }
}
