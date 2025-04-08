<?php

namespace wcf\system\interaction;

/**
 * Represents a disable interaction.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class DisableInteraction extends RpcInteraction
{
    public function __construct(
        string $endpoint,
        ?\Closure $isAvailableCallback = null
    ) {
        parent::__construct(
            'disable',
            $endpoint,
            'wcf.global.button.disable',
            InteractionConfirmationType::Disable,
            '',
            $isAvailableCallback
        );
    }
}
