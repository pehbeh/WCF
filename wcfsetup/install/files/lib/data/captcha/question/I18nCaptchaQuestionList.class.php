<?php

namespace wcf\data\captcha\question;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of captcha question list.
 *
 * @author      Olaf Brain
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @extends I18nDatabaseObjectList<CaptchaQuestion>
 */
class I18nCaptchaQuestionList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['question' => 'questionI18n'];

    /**
     * @inheritDoc
     */
    public $className = CaptchaQuestion::class;
}
