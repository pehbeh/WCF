<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use wcf\system\upload\UploadHandler;

/**
 * Default implementation for file uploads using the AJAX-API.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class AJAXUploadAction extends AJAXProxyAction
{
    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $this->parameters['__files'] = UploadHandler::getUploadHandler('__files');
    }

    /**
     * @return mixed
     */
    protected function sendResponse()
    {
        if (!isset($_POST['isFallback'])) {
            parent::sendResponse();
        }

        $response = new JsonResponse($this->response);
        // IE9 is mad if iframe response is application/json
        $response = $response->withHeader('content-type', 'text/plain');

        return $response;
    }
}
