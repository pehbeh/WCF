<?php

namespace wcf\system\image;


/**
 * Provides some very basic functions to decode WebP images.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 */
final class WebPDecoder
{
    /**
     * Decodes the EXIF data contained in a WebP VP8X container.
     */
    public static function extractExifData(string $filename): array
    {
        // We're offloading the EXIF decoding task for `exif_read_data()` which
        // cannot process WebP.
        if (!\function_exists('exif_read_data')) {
            return [];
        }

        $data = \file_get_contents($filename);
        if (\strlen($data) <= 30) {
            // The RIFF header for VP8X is at least 30 bytes.
            return [];
        }

        // A WebP image must start with "RIFF" in ascii, followed by four bytes
        // for the chunk length and then read "WEBP" at offset 8.
        if (!(\substr($data, 0, 4) === "RIFF" && \substr($data, 8, 4) === "WEBP")) {
            return [];
        }

        // Only VP8X contains EXIF data.
        if (!(\substr($data, 12, 4) === "VP8X")) {
            return [];
        }

        // Check if the EXIF bit is set.
        $flags = \ord(\substr($data, 20, 1));
        $hasExif = $flags & 0b00001000;
        if (!$hasExif) {
            return [];
        }

        // Find the EXIF chunk.
        $exifData = null;
        $offset = 30;
        while ($offset < \strlen($data)) {
            $chunkHeader = \substr($data, $offset, 4);
            // 'V' = uint32LE
            $chunkSize = \unpack('V', \substr($data, $offset + 4, 4))[1];
            $offset += 8;

            if ($chunkHeader !== 'EXIF') {
                // "If Chunk Size is odd, a single padding byte -- which MUST be
                // 0 to conform with RIFF -- is added."
                $paddingByte = $chunkSize % 2;
                $offset += $chunkSize + $paddingByte;

                continue;
            }

            $exifData = \substr($data, $offset, $chunkSize);
        }

        if ($exifData === null) {
            return [];
        }

        // A tiny JPEG used as the host for the EXIF data.
        // See https://github.com/mathiasbynens/small/blob/267b39f682598eebb0dafe7590b1504be79b5cad/jpeg.jpg
        $jpg1x1px = \hex2bin("ffd8ffdb004300030202020202030202020303030304060404040404080606050609080a0a090809090a0c0f0c0a0b0e0b09090d110d0e0f101011100a0c12131210130f101010ffc9000b080001000101011100ffcc000600101005ffda0008010100003f00d2cf20ffd9");

        // The image does not have a JFIF tag and instead directly starts with
        // the quantization table (DQT, 0xFF xDB) after the start of image (SOI,
        // 0xFF 0xD8).
        //
        // This means our offset is always 2. We could further optimize this by
        // ommitting the SOI from the hex string but the \substr() is already
        // quite cheap that it doesn't make sense to obscure the image further.
        \assert($jpg1x1px[0] === "\xFF" && $jpg1x1px[1] === "\xD8");
        \assert($jpg1x1px[2] === "\xFF" && $jpg1x1px[3] === "\xDB");
        $offset = 2;

        // Add the markers for the EXIF sequence in JPEGs.
        $exifData = "\xFF\xE1\xC3\xEF\x45\x78\x69\x66\x00\x00" . $exifData;

        $exif = \exif_read_data(
            \sprintf(
                "data://image/jpeg;base64,%s",
                \base64_encode(\substr($jpg1x1px, 0, $offset) . $exifData . \substr($jpg1x1px, $offset)),
            ),
        );

        if ($exif === false) {
            return [];
        }

        return $exif;
    }
}
