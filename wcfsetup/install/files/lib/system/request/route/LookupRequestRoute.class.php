<?php

namespace wcf\system\request\route;

use wcf\system\application\ApplicationHandler;
use wcf\system\request\ControllerMap;
use wcf\util\FileUtil;

/**
 * Attempts to resolve arbitrary request URLs against the list of known custom
 * controller URLs, optionally recognizing id and title parameter.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 */
final class LookupRequestRoute implements IRequestRoute
{
    /**
     * list of parsed route information
     * @var array{id?: string, title?: string, controller?: string, isDefaultController?: boolean}
     */
    private array $routeData = [];

    /**
     * @inheritDoc
     */
    public function matches($requestURL): bool
    {
        $requestURL = FileUtil::removeLeadingSlash($requestURL);

        if ($requestURL === '') {
            // ignore empty urls and let them be handled by regular routes
            return false;
        }

        $regex = '~^
			(?P<controller>.+?)
			(?:
				/
				(?P<id>[0-9]+)
				(?:
					-
					(?P<title>[^/]+)
				)?
				/?
			)?
		$~x';

        if (\preg_match($regex, $requestURL, $matches)) {
            $application = ApplicationHandler::getInstance()->getActiveApplication()->getAbbreviation();
            if (!empty($matches['controller'])) {
                // check for static controller URLs
                $this->routeData = ControllerMap::getInstance()->resolveCustomController(
                    $application,
                    FileUtil::removeTrailingSlash($matches['controller'])
                );

                if ($this->routeData !== []) {
                    if (!empty($matches['id'])) {
                        $this->routeData['id'] = $matches['id'];

                        if (!empty($matches['title'])) {
                            $this->routeData['title'] = $matches['title'];
                        }
                    }
                }
            }

            if ($this->routeData === []) {
                // try to match the entire url
                $this->routeData = ControllerMap::getInstance()->resolveCustomController(
                    $application,
                    FileUtil::removeTrailingSlash($requestURL)
                );
            }
        }

        if ($this->routeData !== []) {
            $this->routeData['isDefaultController'] = false;

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getRouteData(): array
    {
        return $this->routeData;
    }

    /**
     * @inheritDoc
     * @throws  \BadMethodCallException
     */
    public function buildLink(array $components): string
    {
        throw new \BadMethodCallException(
            'LookupRequestRoute cannot build links, please verify capabilities by calling canHandle() first.'
        );
    }

    /**
     * @inheritDoc
     */
    public function canHandle(array $components): bool
    {
        // this route cannot build routes, it is a one-way resolver
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isACP(): bool
    {
        // lookups are not supported for ACP requests
        return false;
    }
}
