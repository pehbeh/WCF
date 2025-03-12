<?php

namespace wcf\system\like;

use wcf\data\like\ViewableLike;

/**
 * Default interface for viewable like providers.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IViewableLikeProvider
{
    /**
     * Prepares a list of likes for output.
     *
     * @param ViewableLike[] $likes
     * @return void
     */
    public function prepare(array $likes);
}
