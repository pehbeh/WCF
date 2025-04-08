<?php

namespace wcf\system\interaction;

/**
 * Represents a soft-delete interaction.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class SoftDeleteInteraction extends RpcInteraction
{
    public function __construct(
        string $endpoint,
        ?\Closure $isAvailableCallback = null
    ) {
        parent::__construct(
            'soft-delete',
            $endpoint,
            'wcf.global.button.trash',
            InteractionConfirmationType::SoftDelete,
            '',
            $isAvailableCallback
        );
    }
}
