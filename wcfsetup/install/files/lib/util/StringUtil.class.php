<?php

namespace wcf\util;

use ParagonIE\ConstantTime\Hex;
use wcf\system\application\ApplicationHandler;
use wcf\system\request\RouteHandler;
use wcf\system\WCF;

/**
 * Contains string-related functions.
 *
 * @author  Oliver Kliebisch, Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class StringUtil
{
    /**
     * utf8 bytes of the HORIZONTAL ELLIPSIS (U+2026)
     * @var string
     */
    const HELLIP = "\u{2026}";

    /**
     * utf8 bytes of the MINUS SIGN (U+2212)
     * @var string
     */
    const MINUS = "\u{2212}";

    /**
     * @deprecated 5.5 - Use \sha1() directly.
     */
    public static function getHash(string $value): string
    {
        return \sha1($value);
    }

    /**
     * Returns a 40 character hexadecimal string generated using a CSPRNG.
     */
    public static function getRandomID(): string
    {
        return Hex::encode(\random_bytes(20));
    }

    /**
     * Creates an UUID.
     */
    public static function getUUID(): string
    {
        return \sprintf(
            '%04x%04x-%04x-%04x-%02x%02x-%04x%04x%04x',
            // time_low
            \random_int(0, 0xffff),
            \random_int(0, 0xffff),
            // time_mid
            \random_int(0, 0xffff),
            // time_hi_and_version
            \random_int(0, 0x0fff) | 0x4000,
            // clock_seq_hi_and_res
            \random_int(0, 0x3f) | 0x80,
            // clock_seq_low
            \random_int(0, 0xff),
            // node
            \random_int(0, 0xffff),
            \random_int(0, 0xffff),
            \random_int(0, 0xffff)
        );
    }

    /**
     * Converts dos to unix newlines.
     */
    public static function unifyNewlines(string $string): string
    {
        return \preg_replace("%(\r\n)|(\r)%", "\n", $string);
    }

    /**
     * Removes Unicode whitespace characters from the beginning
     * and ending of the given string.
     */
    public static function trim(string $text): string
    {
        // $boundaryCharacters can always be removed when appearing at either the beginning
        // or the end of the input.
        //
        // Cc = Other, Control
        // Zs = Separator, Space
        // Zl = Separator, Line
        // Zp = Separator, Paragraph
        $boundaryCharacters = "\p{Cc}\p{Zs}\p{Zl}\p{Zp}"
            . "\s"
            . "\x{202E}\x{200B}";

        // $fullStringCharacters will be removed if the resulting string consists only of
        // these characters. However they may have a valid use case at the beginning or end
        // provided there *are* printable characters.
        //
        // Cf = Other, Format
        // List of characters as per https://invisible-characters.com/
        $fullStringCharacters = "{$boundaryCharacters}\p{Cf}"
            . "\x{0009}\x{0020}\x{00A0}\x{00AD}\x{034F}\x{061C}\x{115F}\x{1160}\x{17B4}\x{17B5}\x{180E}\x{2000}"
            . "\x{2001}\x{2002}\x{2003}\x{2004}\x{2005}\x{2006}\x{2007}\x{2008}\x{2009}\x{200A}\x{200B}\x{200C}"
            . "\x{200D}\x{200E}\x{200F}\x{202F}\x{205F}\x{2060}\x{2061}\x{2062}\x{2063}\x{2064}\x{206A}\x{206B}"
            . "\x{206C}\x{206D}\x{206E}\x{206F}\x{3000}\x{2800}\x{3164}\x{FEFF}\x{FFA0}\x{1D159}\x{1D173}\x{1D174}"
            . "\x{1D175}\x{1D176}\x{1D177}\x{1D178}\x{1D179}\x{1D17A}";

        // Do not merge the expressions, they are separated for
        // performance reasons.
        $trimmed = \preg_replace("/^[{$boundaryCharacters}]+/u", '', $text);

        // Check if preg_replace() failed, indicating that the
        // input is not valid UTF-8. In this case the original
        // value is returned, because we cannot meaningfully
        // trim inputs that are not UTF-8.
        if ($trimmed === null) {
            return $text;
        }

        $trimmed = \preg_replace("/[{$boundaryCharacters}]+$/u", '', $trimmed);

        if ($trimmed === null) {
            return $text;
        }

        // If the remaining string consists of $fullStringCharacters only, they
        // will all be removed.
        if (\preg_match("/^[{$fullStringCharacters}]+$/u", $trimmed)) {
            return '';
        }

        return $trimmed;
    }

    /**
     * Converts html special characters.
     */
    public static function encodeHTML(string $string): string
    {
        return @\htmlspecialchars(
            $string,
            \ENT_QUOTES | \ENT_SUBSTITUTE | \ENT_HTML401,
            'UTF-8'
        );
    }

    /**
     * Converts javascript special characters.
     */
    public static function encodeJS(string $string): string
    {
        $string = self::unifyNewlines($string);

        return \str_replace(["\\", "'", '"', "\n", "/"], ["\\\\", "\\'", '\\"', '\\n', '\\/'], $string);
    }

    /**
     * Decodes html entities.
     */
    public static function decodeHTML(string $string): string
    {
        $string = \str_ireplace('&nbsp;', ' ', $string); // convert non-breaking spaces to ascii 32; not ascii 160

        return @\html_entity_decode(
            $string,
            \ENT_QUOTES | \ENT_SUBSTITUTE | \ENT_HTML401,
            'UTF-8'
        );
    }

    /**
     * Formats a numeric.
     */
    public static function formatNumeric(int|float $numeric): string
    {
        $formatted = self::getNumberFormatter()->format($numeric);

        if ($numeric < 0) {
            return self::formatNegative($formatted);
        }

        return $formatted;
    }

    /**
     * Formats an integer.
     *
     * @deprecated 6.0 Use `formatNumeric()` instead.
     */
    public static function formatInteger(int $integer): string
    {
        return self::formatNumeric($integer);
    }

    /**
     * Formats a double.
     *
     * @deprecated 6.0 Use `formatNumeric()` instead. Create a custom NumberFormatter for more / less than 2 decimals.
     */
    public static function formatDouble(float $double, int $maxDecimals = 0): string
    {
        $maxDecimals = ($maxDecimals > 0 ? $maxDecimals : 2);

        if ($maxDecimals === 2) {
            return self::formatNumeric($double);
        }

        $locale = WCF::getLanguage()->getLocale();
        $formatter = new \NumberFormatter($locale, \NumberFormatter::DEFAULT_STYLE);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $maxDecimals);

        $formatted = $formatter->format($double);

        if ($double < 0) {
            $formatted = self::formatNegative($formatted);
        }

        return $formatted;
    }

    /**
     * Adds thousands separators to a given number.
     *
     * @deprecated 6.0 Use `formatNumeric()` instead.
     */
    public static function addThousandsSeparator(int|float $number): string
    {
        return self::getNumberFormatter()->format($number);
    }

    /**
     * Replaces the MINUS-HYPHEN with the MINUS SIGN.
     */
    public static function formatNegative(string $number): string
    {
        return \str_replace('-', self::MINUS, $number);
    }

    /**
     * Alias to php ucfirst() function with multibyte support.
     *
     * @deprecated 6.2 Use `\mb_ucfirst()` instead
     */
    public static function firstCharToUpperCase(string $string): string
    {
        return \mb_strtoupper(\mb_substr($string, 0, 1)) . \mb_substr($string, 1);
    }

    /**
     * Alias to php lcfirst() function with multibyte support.
     *
     * @deprecated 6.2 Use `\mb_lcfirst()` instead
     */
    public static function firstCharToLowerCase(string $string): string
    {
        return \mb_strtolower(\mb_substr($string, 0, 1)) . \mb_substr($string, 1);
    }

    /**
     * Alias to php mb_convert_case() function.
     */
    public static function wordsToUpperCase(string $string): string
    {
        return \mb_convert_case($string, \MB_CASE_TITLE);
    }

    /**
     * Alias to php str_ireplace() function with UTF-8 support.
     *
     * This function is considered to be slow, if $search contains
     * only ASCII characters, please use str_ireplace() instead.
     */
    public static function replaceIgnoreCase(string $search, string $replace, string $subject, int &$count = 0): string
    {
        $startPos = \mb_strpos(\mb_strtolower($subject), \mb_strtolower($search));
        if ($startPos === false) {
            return $subject;
        } else {
            $endPos = $startPos + \mb_strlen($search);
            $count++;

            return \mb_substr($subject, 0, $startPos) . $replace . self::replaceIgnoreCase(
                $search,
                $replace,
                \mb_substr($subject, $endPos),
                $count
            );
        }
    }

    /**
     * @return list<string>
     * @deprecated 5.5 Use \mb_str_split() instead.
     */
    public static function split(string $string, int $length = 1): array
    {
        $result = [];
        for ($i = 0, $max = \mb_strlen($string); $i < $max; $i += $length) {
            $result[] = \mb_substr($string, $i, $length);
        }

        return $result;
    }

    /**
     * @deprecated 5.5 Use \str_starts_with() instead. If a case-insensitive comparison is desired, manually call \mb_strtolower on both parameters.
     */
    public static function startsWith(string $haystack, string $needle, bool $ci = false): bool
    {
        if ($ci) {
            $haystack = \mb_strtolower($haystack);
            $needle = \mb_strtolower($needle);
        }
        // using mb_substr and === is MUCH faster for long strings then using indexOf.
        return \mb_substr($haystack, 0, \mb_strlen($needle)) === $needle;
    }

    /**
     * @deprecated 5.5 Use \str_ends_with() instead. If a case-insensitive comparison is desired, manually call \mb_strtolower on both parameters.
     */
    public static function endsWith(string $haystack, string $needle, bool $ci = false): bool
    {
        if ($ci) {
            $haystack = \mb_strtolower($haystack);
            $needle = \mb_strtolower($needle);
        }
        $length = \mb_strlen($needle);
        if ($length === 0) {
            return true;
        }

        return \mb_substr($haystack, $length * -1) === $needle;
    }

    /**
     * Alias to php str_pad function with multibyte support.
     */
    public static function pad(string $input, int $padLength, string  $padString = ' ', int $padType = \STR_PAD_RIGHT): string
    {
        $additionalPadding = \strlen($input) - \mb_strlen($input);

        return \str_pad($input, $padLength + $additionalPadding, $padString, $padType);
    }

    /**
     * Unescapes escaped characters in a string.
     */
    public static function unescape(string $string, string $chars = '"'): string
    {
        for ($i = 0, $j = \strlen($chars); $i < $j; $i++) {
            $string = \str_replace('\\' . $chars[$i], $chars[$i], $string);
        }

        return $string;
    }

    /**
     * Takes a numeric HTML entity value and returns the appropriate UTF-8 bytes.
     */
    public static function getCharacter(int $dec): string
    {
        if ($dec < 128) {
            $utf = \chr($dec);
        } elseif ($dec < 2048) {
            $utf = \chr(192 + (($dec - ($dec % 64)) / 64));
            $utf .= \chr(128 + ($dec % 64));
        } else {
            $utf = \chr(224 + (($dec - ($dec % 4096)) / 4096));
            $utf .= \chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
            $utf .= \chr(128 + ($dec % 64));
        }

        return $utf;
    }

    /**
     * Converts UTF-8 to Unicode
     * @see     http://www1.tip.nl/~t876506/utf8tbl.html
     */
    public static function getCharValue(string $c): int
    {
        $ud = 0;
        if (\ord($c[0]) <= 127) {
            $ud = \ord($c[0]);
        }
        if (\ord($c[0]) >= 192 && \ord($c[0]) <= 223) {
            $ud = (\ord($c[0]) - 192) * 64 + (\ord($c[1]) - 128);
        }
        if (\ord($c[0]) >= 224 && \ord($c[0]) <= 239) {
            $ud = (\ord($c[0]) - 224) * 4096 + (\ord($c[1]) - 128) * 64 + (\ord($c[2]) - 128);
        }
        if (\ord($c[0]) >= 240 && \ord($c[0]) <= 247) {
            $ud = (\ord($c[0]) - 240) * 262144 + (\ord($c[1]) - 128) * 4096 + (\ord($c[2]) - 128) * 64 + (\ord($c[3]) - 128);
        }
        if (\ord($c[0]) >= 248 && \ord($c[0]) <= 251) {
            $ud = (\ord($c[0]) - 248) * 16777216 + (\ord($c[1]) - 128) * 262144 + (\ord($c[2]) - 128) * 4096 + (\ord($c[3]) - 128) * 64 + (\ord($c[4]) - 128);
        }
        if (\ord($c[0]) >= 252 && \ord($c[0]) <= 253) {
            $ud = (\ord($c[0]) - 252) * 1073741824 + (\ord($c[1]) - 128) * 16777216 + (\ord($c[2]) - 128) * 262144 + (\ord($c[3]) - 128) * 4096 + (\ord($c[4]) - 128) * 64 + (\ord($c[5]) - 128);
        }
        if (\ord($c[0]) >= 254) {
            $ud = false; // error
        }

        return $ud;
    }

    /**
     * Returns html entities of all characters in the given string.
     */
    public static function encodeAllChars(string $string): string
    {
        $result = '';
        for ($i = 0, $j = \mb_strlen($string); $i < $j; $i++) {
            $char = \mb_substr($string, $i, 1);
            $result .= '&#' . self::getCharValue($char) . ';';
        }

        return $result;
    }

    /**
     * Returns true if the given string contains only ASCII characters.
     */
    public static function isASCII(string $string): bool
    {
        return !!\preg_match('/^[\x00-\x7F]*$/', $string);
    }

    /**
     * Returns true if the given string is utf-8 encoded.
     * @see     http://www.w3.org/International/questions/qa-forms-utf-8
     */
    public static function isUTF8(string $string): bool
    {
        return !!\preg_match('/^(
				[\x09\x0A\x0D\x20-\x7E]*		# ASCII
			|	[\xC2-\xDF][\x80-\xBF]			# non-overlong 2-byte
			|	\xE0[\xA0-\xBF][\x80-\xBF]		# excluding overlongs
			|	[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}	# straight 3-byte
			|	\xED[\x80-\x9F][\x80-\xBF]		# excluding surrogates
			|	\xF0[\x90-\xBF][\x80-\xBF]{2}		# planes 1-3
			|	[\xF1-\xF3][\x80-\xBF]{3}		# planes 4-15
			|	\xF4[\x80-\x8F][\x80-\xBF]{2}		# plane 16
			)*$/x', $string);
    }

    /**
     * Escapes the closing cdata tag.
     */
    public static function escapeCDATA(string $string): string
    {
        return \str_replace(']]>', ']]]]><![CDATA[>', $string);
    }

    /**
     * @deprecated 6.0 Use `\mb_convert_encoding()` directly.
     */
    public static function convertEncoding(string $inCharset, string $outCharset, string $string): string
    {
        return \mb_convert_encoding($string, $outCharset, $inCharset);
    }

    /**
     * Strips HTML tags from a string.
     */
    public static function stripHTML(string $string): string
    {
        $string = \preg_replace('~<!--(.*?)-->~', '', $string);

        return \preg_replace(
            // Note the possessive quantifier '*+' at the end of the
            // regular expression. This quantifier needs to be possessive
            // for performance reasons, because otherwise catastrophic
            // backtracking will occur due to the use of two quantifiers
            // right next to each other (the + in the first alternative and
            // the * repating the whole alternation). It also prevents trying
            // all the alternatives once again for incorrectly quoted attributes:
            // For '<foo bar=">' the regular expression would retry matching a
            // quote for each =, r, a, b, ... if the quantifier would not be
            // possessive.
            '/<\/?[a-zA-Z](?:[^>"\']+|"[^"]*"|\'[^\']*\')*+>/',
            '',
            $string
        );
    }

    /**
     * Returns false if the given word is forbidden by given word filter.
     */
    public static function executeWordFilter(string $word, string $filter): bool
    {
        $filter = self::trim($filter);
        $word = \mb_strtolower($word);

        if ($filter != '') {
            $forbiddenNames = \explode("\n", \mb_strtolower(self::unifyNewlines($filter)));
            foreach ($forbiddenNames as $forbiddenName) {
                // ignore empty lines in between actual values
                $forbiddenName = self::trim($forbiddenName);
                if (empty($forbiddenName)) {
                    continue;
                }

                if (\str_contains($forbiddenName, '*')) {
                    $forbiddenName = \str_replace('\*', '.*', \preg_quote($forbiddenName, '/'));
                    if (\preg_match('/^' . $forbiddenName . '$/s', $word)) {
                        return false;
                    }
                } else {
                    if ($word == $forbiddenName) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Truncates the given string to a certain number of characters.
     */
    public static function truncate(
        string $string,
        int $length = 80,
        string $etc = self::HELLIP,
        bool $breakWords = false
    ): string {
        if ($length == 0) {
            return '';
        }

        if (\mb_strlen($string) > $length) {
            $length -= \mb_strlen($etc);

            if (!$breakWords) {
                $string = \preg_replace('/\\s+?(\\S+)?$/', '', \mb_substr($string, 0, $length + 1));
            }

            return \mb_substr($string, 0, $length) . $etc;
        } else {
            return $string;
        }
    }

    /**
     * Truncates a string containing HTML code and keeps the HTML syntax intact.
     */
    public static function truncateHTML(
        string $string,
        int $length = 500,
        string $etc = self::HELLIP,
        bool $breakWords = false
    ): string {
        if (\mb_strlen(self::stripHTML($string)) <= $length) {
            return $string;
        }
        $openTags = [];
        $truncatedString = '';

        // initialize length counter with the ending length
        $totalLength = \mb_strlen($etc);

        \preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $string, $tags, \PREG_SET_ORDER);

        foreach ($tags as $tag) {
            // ignore void elements
            if (
                !\preg_match(
                    '/^(area|base|br|col|embed|hr|img|input|keygen|link|menuitem|meta|param|source|track|wbr)$/s',
                    $tag[2]
                )
            ) {
                // look for opening tags
                if (\preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                    \array_unshift($openTags, $tag[2]);
                }
                /**
                 * look for closing tags and check if this tag has a corresponding opening tag
                 * and omit the opening tag if it has been closed already
                 */
                elseif (\preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                    $position = \array_search($closeTag[1], $openTags);
                    if ($position !== false) {
                        \array_splice($openTags, $position, 1);
                    }
                }
            }
            // append tag
            $truncatedString .= $tag[1];

            // get length of the content without entities. If the content is too long, keep entities intact
            $decodedContent = self::decodeHTML($tag[3]);
            $contentLength = \mb_strlen($decodedContent);
            if ($contentLength + $totalLength > $length) {
                if (!$breakWords) {
                    if (\preg_match('/^(.{1,' . ($length - $totalLength) . '}) /s', $decodedContent, $match)) {
                        $truncatedString .= self::encodeHTML($match[1]);
                    }

                    break;
                }

                $left = $length - $totalLength;
                $entitiesLength = 0;
                if (
                    \preg_match_all(
                        '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i',
                        $tag[3],
                        $entities,
                        \PREG_OFFSET_CAPTURE
                    )
                ) {
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entitiesLength <= $left) {
                            $left--;
                            $entitiesLength += \mb_strlen($entity[0]);
                        } else {
                            break;
                        }
                    }
                }
                $truncatedString .= \mb_substr($tag[3], 0, $left + $entitiesLength);
                break;
            } else {
                $truncatedString .= $tag[3];
                $totalLength += $contentLength;
            }
            if ($totalLength >= $length) {
                break;
            }
        }

        // close all open tags
        foreach ($openTags as $tag) {
            $truncatedString .= '</' . $tag . '>';
        }

        // add etc
        $truncatedString .= $etc;

        return $truncatedString;
    }

    /**
     * Generates an anchor tag from given URL.
     */
    public static function getAnchorTag(
        string $url,
        string $title = '',
        bool $encodeTitle = true,
        bool $isUgc = false
    ): string {
        $url = self::trim($url);

        // cut visible url
        if (empty($title)) {
            // use URL and remove protocol and www subdomain
            $title = \preg_replace('~^(?:https?|ftps?)://(?:www\.)?~i', '', $url);

            if (\mb_strlen($title) > 60) {
                $title = \mb_substr($title, 0, 30) . self::HELLIP . \mb_substr($title, -25);
            }

            if (!$encodeTitle) {
                $title = self::encodeHTML($title);
            }
        }

        return '<a ' . self::getAnchorTagAttributes(
            $url,
            $isUgc
        ) . '>' . ($encodeTitle ? self::encodeHTML($title) : $title) . '</a>';
    }

    /**
     * Generates the attributes for an anchor tag from given URL.
     *
     * @since       5.3
     */
    public static function getAnchorTagAttributes(string $url, bool $isUgc = false): string
    {
        $external = true;
        if (ApplicationHandler::getInstance()->isInternalURL($url)) {
            $external = false;
            $url = \preg_replace('~^https?://~', RouteHandler::getProtocol(), $url);
        }

        $attributes = 'href="' . self::encodeHTML($url) . '"';
        if ($external) {
            $attributes .= ' class="externalURL"';
            $rel = 'nofollow';
            if (EXTERNAL_LINK_TARGET_BLANK) {
                $rel .= ' noopener';
                $attributes .= 'target="_blank"';
            }
            if ($isUgc) {
                $rel .= ' ugc';
            }

            $attributes .= ' rel="' . $rel . '"';
        }

        return $attributes;
    }

    /**
     * Splits given string into smaller chunks.
     */
    public static function splitIntoChunks(string $string, int $length = 75, string $break = "\r\n"): string
    {
        return \mb_ereg_replace('.{' . $length . '}', "\\0" . $break, $string);
    }

    /**
     * Simple multi-byte safe wordwrap() function.
     */
    public static function wordwrap(string $string, int $width = 50, string $break = ' '): string
    {
        $result = '';
        $substrings = \explode($break, $string);

        foreach ($substrings as $substring) {
            $length = \mb_strlen($substring);
            if ($length > $width) {
                $j = \ceil($length / $width);

                for ($i = 0; $i < $j; $i++) {
                    if (!empty($result)) {
                        $result .= $break;
                    }
                    if ($width * ($i + 1) > $length) {
                        $result .= \mb_substr($substring, $width * $i);
                    } else {
                        $result .= \mb_substr($substring, $width * $i, $width);
                    }
                }
            } else {
                if (!empty($result)) {
                    $result .= $break;
                }
                $result .= $substring;
            }
        }

        return $result;
    }

    /**
     * Shortens numbers larger than 1000 by using unit suffixes.
     */
    public static function getShortUnit(int $number): string
    {
        $unitSuffix = '';

        if ($number >= 1000000) {
            $number /= 1000000;
            if ($number > 10) {
                $number = \floor($number);
            } else {
                $number = \round($number, 1);
            }
            $unitSuffix = 'M';
        } elseif ($number >= 1000) {
            $number /= 1000;
            if ($number > 10) {
                $number = \floor($number);
            } else {
                $number = \round($number, 1);
            }
            $unitSuffix = 'k';
        }

        return self::formatNumeric($number) . $unitSuffix;
    }

    /**
     * Normalizes a string representing comma-separated values by making sure
     * that the separator is just a comma, not a combination of whitespace and
     * a comma.
     *
     * @since   3.1
     */
    public static function normalizeCsv(string $string): string
    {
        return \implode(',', ArrayUtil::trim(\explode(',', $string)));
    }

    private static function getNumberFormatter(): \NumberFormatter
    {
        static $formatters = [];

        $locale = WCF::getLanguage()->getLocale();
        if (!isset($formatters[$locale])) {
            $formatters[$locale] = new \NumberFormatter($locale, \NumberFormatter::DEFAULT_STYLE);
            $formatters[$locale]->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 2);
        }

        return $formatters[$locale];
    }

    /**
     * Forbid creation of StringUtil objects.
     */
    private function __construct()
    {
        // does nothing
    }
}
