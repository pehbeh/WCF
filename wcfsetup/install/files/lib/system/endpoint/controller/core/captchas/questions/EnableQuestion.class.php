<?php

namespace wcf\system\endpoint\controller\core\captchas\questions;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\captcha\question\CaptchaQuestion;
use wcf\data\captcha\question\CaptchaQuestionAction;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for enabling captcha questions.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[PostRequest('/core/captchas/questions/{id:\d+}/enable')]
final class EnableQuestion implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $question = Helper::fetchObjectFromRequestParameter($variables['id'], CaptchaQuestion::class);

        $this->assertQuestionCanBeEnabled($question);

        (new CaptchaQuestionAction([$question], 'toggle'))->executeAction();

        return new JsonResponse([]);
    }

    private function assertQuestionCanBeEnabled(CaptchaQuestion $question): void
    {
        WCF::getSession()->checkPermissions(['admin.captcha.canManageCaptchaQuestion']);

        if (!$question->isDisabled) {
            throw new PermissionDeniedException();
        }
    }
}
