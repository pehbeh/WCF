<?php

namespace wcf\system\cache\eager;

use wcf\data\captcha\question\CaptchaQuestion;
use wcf\data\captcha\question\CaptchaQuestionList;

/**
 * Eager cache implementation for captcha questions.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractEagerCache<array<int, CaptchaQuestion>>
 */
final class CaptchaQuestionCache extends AbstractEagerCache
{
    #[\Override]
    protected function getCacheData(): array
    {
        $questionList = new CaptchaQuestionList();
        $questionList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $questionList->readObjects();

        return $questionList->getObjects();
    }
}
