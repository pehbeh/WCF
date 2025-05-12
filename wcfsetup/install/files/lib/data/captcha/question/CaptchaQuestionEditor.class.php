<?php

namespace wcf\data\captcha\question;

use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\eager\CaptchaQuestionCache;

/**
 * Provides functions to edit captcha questions.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       CaptchaQuestion
 * @extends DatabaseObjectEditor<CaptchaQuestion>
 * @implements IEditableCachedObject<CaptchaQuestion>
 */
class CaptchaQuestionEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = CaptchaQuestion::class;

    /**
     * @inheritDoc
     */
    public static function resetCache()
    {
        (new CaptchaQuestionCache())->rebuild();
    }
}
