<?php

namespace wcf\system\interaction;

/**
 * Represents a trash interaction.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class TrashInteraction extends RpcInteraction
{
    public function __construct(
        string $endpoint,
        ?\Closure $isAvailableCallback = null
    ) {
        parent::__construct(
            'trash',
            $endpoint,
            'wcf.global.button.trash',
            InteractionConfirmationType::SoftDelete,
            '',
            $isAvailableCallback
        );
    }
}
