<?php

namespace wcf\data\user\rank;

use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\form\builder\field\UploadFormField;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a user rank.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property-read   int $rankID         unique id of the user rank
 * @property-read   int $groupID        id of the user group to which the user rank belongs
 * @property-read   int $requiredPoints     minimum number of user activity points required for a user to get the user rank
 * @property-read   string $rankTitle      title of the user rank or name of the language item which contains the rank
 * @property-read   string $cssClassName       css class name used when displaying the user rank
 * @property-read   string $rankImage      (WCF relative) path to the image displayed next to the rank or empty if no rank image exists
 * @property-read   int $repeatImage        number of times the rank image is displayed
 * @property-read   int $requiredGender     numeric representation of the user's gender required for the user rank (see `UserProfile::GENDER_*` constants) or 0 if no specific gender is required
 * @property-read   int $hideTitle      hides the generic title of the rank, but not custom titles, `0` to show the title at all times
 */
class UserRank extends DatabaseObject implements ITitledObject
{
    public const RANK_IMAGE_DIR = 'images/rank/';

    /**
     * Returns the image of this user rank.
     *
     * @return  string      html code
     */
    public function getImage()
    {
        if ($this->rankImage) {
            $image = '<img src="' . WCF::getPath() . self::RANK_IMAGE_DIR . StringUtil::encodeHTML($this->rankImage) . '" alt="">';
            if ($this->repeatImage > 1) {
                $image = \str_repeat($image, $this->repeatImage);
            }

            return $image;
        }

        return '';
    }

    /**
     * @inheritDoc
     * @since   5.2
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get($this->rankTitle);
    }

    /**
     * Returns true if the generic rank title should be displayed.
     *
     * @return      bool
     */
    public function showTitle()
    {
        return !$this->rankImage || !$this->hideTitle;
    }

    /**
     * @see UploadFormField::updatedObject()
     * @return list<string>
     */
    public function getRankImageFileUploadFileLocations(): array
    {
        if (!$this->rankImage) {
            return [];
        }

        return [WCF_DIR . self::RANK_IMAGE_DIR . $this->rankImage];
    }
}
