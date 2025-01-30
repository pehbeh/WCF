<?php

namespace wcf\system\interaction\bulk;

use wcf\system\interaction\InteractionConfirmationType;

/**
 * Represents a bulk restore interaction.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class BulkRestoreInteraction extends BulkRpcInteraction
{
    public function __construct(
        string $endpoint,
        ?\Closure $isAvailableCallback = null
    ) {
        parent::__construct(
            'restore',
            $endpoint,
            'wcf.global.button.restore',
            InteractionConfirmationType::Restore,
            '',
            $isAvailableCallback
        );
    }
}
