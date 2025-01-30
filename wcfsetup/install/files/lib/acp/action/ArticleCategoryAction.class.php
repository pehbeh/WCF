<?php

namespace wcf\acp\action;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use wcf\data\category\CategoryNodeTree;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\Psr15DialogForm;
use wcf\system\WCF;

/**
 * Handles setting the category for articles.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class ArticleCategoryAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!WCF::getSession()->getPermission("admin.content.article.canManageArticle")) {
            throw new PermissionDeniedException();
        }

        $parameters = Helper::mapQueryParameters(
            $request->getQueryParams(),
            <<<'EOT'
                array {
                    objectIDs: positive-int[]
                }
                EOT
        );

        if ($parameters['objectIDs'] === []) {
            throw new IllegalLinkException();
        }

        $form = $this->getForm();

        if ($request->getMethod() === 'GET') {
            return $form->toResponse();
        } elseif ($request->getMethod() === 'POST') {
            $response = $form->validateRequest($request);
            if ($response !== null) {
                return $response;
            }

            $data = $form->getData()['data'];

            WCF::getDB()->beginTransaction();

            $sql = "UPDATE wcf1_article
                    SET    categoryID = ?
                    WHERE  articleID = ?";
            $statement = WCF::getDB()->prepare($sql);

            foreach ($parameters['objectIDs'] as $articleID) {
                $statement->execute([$data['categoryID'], $articleID]);
            }

            WCF::getDB()->commitTransaction();

            return new JsonResponse([]);
        } else {
            throw new \LogicException('Unreachable');
        }
    }

    private function getForm(): Psr15DialogForm
    {
        $form = new Psr15DialogForm(
            static::class,
            WCF::getLanguage()->get('wcf.article.button.setCategory')
        );
        $form->appendChildren([
            SingleSelectionFormField::create('categoryID')
                ->label('wcf.acp.article.category')
                ->options((new CategoryNodeTree('com.woltlab.wcf.article.category'))->getIterator(), true)
                ->required()
        ]);

        $form->markRequiredFields(false);
        $form->build();

        return $form;
    }
}
