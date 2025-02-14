<?php

namespace wcf\system\message\unfurl;

use BadMethodCallException;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use ValueError;
use wcf\system\io\http\RedirectGuard;
use wcf\system\io\HttpFactory;
use wcf\system\language\LanguageFactory;
use wcf\system\message\unfurl\exception\DownloadFailed;
use wcf\system\message\unfurl\exception\ParsingFailed;
use wcf\system\message\unfurl\exception\UrlInaccessible;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;
use wcf\util\Url;

/**
 * Helper class to unfurl specific urls.
 *
 * @author      Joshua Ruesweg
 * @copyright   2001-2021 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.4
 */
final class UnfurlResponse
{
    /**
     * 10 Mebibyte
     */
    private const MAX_SIZE = (10 * (1 << 20));

    /**
     * 3 Mebibyte
     */
    public const MAX_IMAGE_SIZE = (3 * (1 << 20));

    /**
     * @var ?ClientInterface
     */
    private static $httpClient;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var ?string
     */
    private $body;

    /**
     * @var string
     */
    private $responseCharset = "UTF-8";

    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * @var \DomXPath
     */
    private $domXPath;

    /**
     * Fetches a given Url and returns an UnfurlResponse instance.
     *
     * @throws ParsingFailed If the body cannot be parsed (e.g. the url is an image).
     * @throws DownloadFailed If the url can not be downloaded. This can be a temporary error.
     * @throws UrlInaccessible If the url is inaccessible (e.g. sends status code 403).
     */
    public static function fetch(string $url): self
    {
        if (!Url::is($url)) {
            throw new \InvalidArgumentException('Given URL "' . $url . '" is not a valid URL.');
        }

        try {
            $defaultLanguage = LanguageFactory::getInstance()->getDefaultLanguage();

            $acceptLanguages = [];
            $acceptLanguages[] = $defaultLanguage->getBcp47();
            if ($defaultLanguage->getBcp47() != $defaultLanguage->languageCode) {
                $acceptLanguages[] = \sprintf("%s; q=0.8", $defaultLanguage->languageCode);
            }
            $acceptLanguages[] = "*; q=0.1";

            $request = new Request('GET', $url, [
                'accept' => 'text/html',
                'range' => \sprintf('bytes=%d-%d', 0, self::MAX_SIZE - 1),
                'accept-encoding' => 'gzip',
                'accept-language' => \implode(', ', $acceptLanguages),
            ]);
            $response = self::getHttpClient()->send($request);

            return new self($response);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();

            if (self::isUrlInaccessible($response)) {
                $message = "Request failed with status code {$response->getStatusCode()}.";

                throw new UrlInaccessible($message, $response->getStatusCode(), $e);
            } else {
                throw new DownloadFailed("Could not download content.", $response->getStatusCode(), $e);
            }
        } catch (ClientExceptionInterface $e) {
            throw new DownloadFailed("Could not download content.", 0, $e);
        }
    }

    /**
     * @throws ParsingFailed If the body cannot be parsed (e.g. the url is an image).
     * @throws DownloadFailed If the url can not be downloaded. This can be a temporary error.
     */
    private function __construct(Response $response)
    {
        $this->response = $response;

        $this->readBody();
        $this->readDomDocument();
    }

    /**
     * Reads the body of the given url and converts the body to utf-8.
     */
    private function readBody(): void
    {
        $this->validateHeaders();

        $this->body = "";
        while (!$this->response->getBody()->eof()) {
            try {
                $this->body .= $this->response->getBody()->read(8192);
            } catch (\RuntimeException $e) {
                throw new DownloadFailed(
                    'Failed to read response body.',
                    0,
                    $e
                );
            }

            if ($this->response->getBody()->tell() >= self::MAX_SIZE) {
                break;
            }
        }
        $this->response->getBody()->close();

        if ($this->responseCharset !== 'UTF-8') {
            try {
                $this->body = \mb_convert_encoding($this->body, 'UTF-8', $this->responseCharset);
            } catch (Exception | ValueError $e) {
                throw new ParsingFailed(
                    "Could not parse body, due an invalid charset.",
                    0,
                    $e
                );
            }
        }
    }

    private function validateHeaders(): void
    {
        $headers = $this->response->getHeader('content-type');
        if (\count($headers) !== 1) {
            throw new ParsingFailed("Expected exactly 1 'content-type' header.");
        }
        $header = $headers[0];
        $pieces = ArrayUtil::trim(\explode(';', $header));
        $contentType = \array_shift($pieces);
        if ($contentType !== 'text/html') {
            throw new ParsingFailed("Expected 'text/html' as the 'content-type'.");
        }

        $charset = null;
        foreach ($pieces as $parameter) {
            $parts = ArrayUtil::trim(\explode('=', $parameter, 2));
            if (\count($parts) !== 2) {
                throw new ParsingFailed("Invalid 'content-type' header: Invalid parameter.");
            }
            if ($parts[0] === 'charset') {
                if ($charset) {
                    throw new ParsingFailed("Invalid 'content-type' header: Duplicate charset.");
                }
                $charset = $parts[1];
            }
        }

        if ($charset) {
            $this->responseCharset = \mb_strtoupper($charset);
        }
    }

    /**
     * Creates the DomDocument.
     *
     * @throws ParsingFailed If the body cannot be parsed (e.g. the url is an JSON file).
     */
    private function readDomDocument(): void
    {
        $useInternalErrors = \libxml_use_internal_errors(true);
        \libxml_clear_errors();
        try {
            $this->domDocument = new \DOMDocument();
            if (!$this->domDocument->loadHTML('<?xml version="1.0" encoding="UTF-8"?>' . $this->body)) {
                throw new ParsingFailed("DOMDocument::loadHTML() failed");
            }

            $this->domXPath = new \DOMXPath($this->domDocument);
        } finally {
            \libxml_use_internal_errors($useInternalErrors);
            \libxml_clear_errors();
        }
    }

    /**
     * Determines the title of the website.
     */
    public function getTitle(): ?string
    {
        if (!empty($this->body)) {
            // og:title
            $list = $this->domXPath->query("//meta[@property='og:title']");
            foreach ($list as $node) {
                /** @var \DOMElement $node */
                return StringUtil::trim($node->getAttribute("content"));
            }

            // title tag
            $list = $this->domXPath->query("//title");
            foreach ($list as $node) {
                return StringUtil::trim($node->nodeValue);
            }
        }

        return null;
    }

    /**
     * Determines the description of the website.
     */
    public function getDescription(): ?string
    {
        if (!empty($this->body)) {
            // og:description
            $list = $this->domXPath->query("//meta[@property='og:description']");
            foreach ($list as $node) {
                /** @var \DOMElement $node */
                return StringUtil::trim($node->getAttribute("content"));
            }
        }

        return null;
    }

    /**
     * Returns the image url for the current url.
     */
    public function getImageUrl(): ?string
    {
        if (!empty($this->body)) {
            // og:image
            $list = $this->domXPath->query("//meta[@property='og:image']");
            foreach ($list as $node) {
                /** @var \DOMElement $node */
                return $node->getAttribute("content");
            }

            // og:image:url
            $list = $this->domXPath->query("//meta[@property='og:image:url']");
            foreach ($list as $node) {
                /** @var \DOMElement $node */
                return $node->getAttribute("content");
            }
        }

        return null;
    }

    /**
     * Returns the Response for the used image.
     *
     * @throws BadMethodCallException If the url does not have an image.
     * @throws DownloadFailed If the url can not be downloaded. This can be a temporary error.
     * @throws UrlInaccessible If the url is inaccessible (e.g. sends status code 403).
     */
    public function getImage(): ResponseInterface
    {
        if (!$this->getImageUrl()) {
            throw new BadMethodCallException("This url does not have an image.");
        }

        try {
            $request = new Request('GET', $this->getImageUrl(), [
                'accept' => 'image/*',
            ]);

            return self::getHttpClient()->send($request);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();

            if (self::isUrlInaccessible($response)) {
                $message = "Request failed with status code {$response->getStatusCode()}.";

                throw new UrlInaccessible($message, $response->getStatusCode(), $e);
            } else {
                throw new DownloadFailed("Could not download content.", $response->getStatusCode(), $e);
            }
        } catch (ClientExceptionInterface $e) {
            throw new DownloadFailed("Could not download content.", 0, $e);
        }
    }

    private static function isUrlInaccessible(Response $response): bool
    {
        switch ($response->getStatusCode()) {
            case 400: // Bad Request
            case 401: // Unauthorized
            case 402: // Payment Required
            case 403: // Forbidden
            case 404: // Not Found
            case 406: // Not Acceptable
                return true;
        }

        return false;
    }

    /**
     * Returns a "static" instance of the HTTP client to use to allow
     * for TCP connection reuse.
     */
    private static function getHttpClient(): ClientInterface
    {
        if (!self::$httpClient) {
            self::$httpClient = HttpFactory::makeClient([
                RequestOptions::TIMEOUT => 10,
                RequestOptions::STREAM => true,
                RequestOptions::HEADERS => [
                    'user-agent' => HttpFactory::getDefaultUserAgent("URL Unfurling; like Twitterbot"),
                ],
                RequestOptions::ALLOW_REDIRECTS => [
                    'on_redirect' => new RedirectGuard(),
                ],
            ]);
        }

        return self::$httpClient;
    }
}
