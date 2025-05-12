<?php

namespace wcf\system\cache\builder;

use wcf\system\cache\eager\CaptchaQuestionCache;

/**
 * Caches the enabled captcha questions.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @deprecated 6.2 use `CaptchaQuestionCache` instead
 */
class CaptchaQuestionCacheBuilder extends AbstractLegacyCacheBuilder
{
    #[\Override]
    protected function rebuild(array $parameters): array
    {
        return (new CaptchaQuestionCache())->getCache();
    }

    #[\Override]
    public function reset(array $parameters = [])
    {
        (new CaptchaQuestionCache())->rebuild();
    }
}
