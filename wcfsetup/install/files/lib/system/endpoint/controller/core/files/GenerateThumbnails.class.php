<?php

namespace wcf\system\endpoint\controller\core\files;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use wcf\data\file\File;
use wcf\data\file\thumbnail\FileThumbnail;
use wcf\data\file\thumbnail\FileThumbnailList;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\file\processor\FileProcessor;

/**
 * Generates a thumbnail for a file.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 */
#[PostRequest('/core/files/{id:\d+}/generate-thumbnails')]
final class GenerateThumbnails implements IController
{
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $file = Helper::fetchObjectFromRequestParameter($variables['id'], File::class);

        FileProcessor::getInstance()->generateWebpVariant($file);
        FileProcessor::getInstance()->generateThumbnails($file);

        $thumbnails = [];
        foreach ($this->getThumbnails($file) as $thumbnail) {
            $thumbnails[] = [
                'identifier' => $thumbnail->identifier,
                'link' => $thumbnail->getLink(),
            ];
        }

        return new JsonResponse($thumbnails);
    }

    /**
     * @return FileThumbnail[]
     */
    private function getThumbnails(File $file): array
    {
        $thumbnailList = new FileThumbnailList();
        $thumbnailList->getConditionBuilder()->add("fileID = ?", [$file->fileID]);
        $thumbnailList->readObjects();

        return $thumbnailList->getObjects();
    }
}
