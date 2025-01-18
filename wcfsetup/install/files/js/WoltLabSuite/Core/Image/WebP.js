/**
 * Provides helper functions to decode and encode WebP images. The exported
 * image will always be VP8X for simplicity.
 *
 * @author    Alexander Ebert
 * @copyright 2001-2025 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.1
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.parseWebPFromBuffer = parseWebPFromBuffer;
    function decodeHeader(uint32BE) {
        switch (uint32BE) {
            case 0x414c5048:
                return "ALPH" /* ChunkHeader.ALPH */;
            case 0x414e494d:
                return "ANIM" /* ChunkHeader.ANIM */;
            case 0x414e4d46:
                return "ANMF" /* ChunkHeader.ANMF */;
            case 0x45584946:
                return "EXIF" /* ChunkHeader.EXIF */;
            case 0x49434350:
                return "ICCP" /* ChunkHeader.ICCP */;
            case 0x52494646:
                return "RIFF" /* ChunkHeader.RIFF */;
            case 0x56503820:
                return "VP8 " /* ChunkHeader.VP8 */;
            case 0x5650384c:
                return "VP8L" /* ChunkHeader.VP8L */;
            case 0x56503858:
                return "VP8X" /* ChunkHeader.VP8X */;
            case 0x57454250:
                return "WEBP" /* ChunkHeader.WEBP */;
            case 0x584d5020:
                return "XMP " /* ChunkHeader.XMP */;
            default:
                return uint32BE;
        }
    }
    class WebP {
        #buffer;
        #chunks;
        #height;
        #width;
        constructor(buffer, width, height, chunks) {
            this.#buffer = buffer;
            this.#chunks = chunks;
            this.#height = height;
            this.#width = width;
        }
        getExifData() {
            for (const [chunkHeader, offset, chunkSize] of this.#chunks) {
                if (chunkHeader === "EXIF" /* ChunkHeader.EXIF */) {
                    return new Uint8Array(this.#buffer.slice(offset, offset + chunkSize));
                }
            }
            return undefined;
        }
        exportWithExif(exif) {
            // The EXIF might originate from a JPEG thus we need to strip the header.
            if (exif[0] === 0xff && exif[1] === 0xe1 && exif[2] === 0xc3 && exif[3] === 0xef) {
                exif = exif.slice(10);
            }
            const iccp = this.#chunks.find(([header]) => header === "ICCP" /* ChunkHeader.ICCP */);
            const anim = this.#chunks.find(([header]) => header === "ANIM" /* ChunkHeader.ANIM */);
            const imageData = [];
            let hasAlpha = false;
            if (anim === undefined) {
                const alpha = this.#chunks.find(([header]) => header === "ALPH" /* ChunkHeader.ALPH */);
                if (alpha !== undefined) {
                    imageData.push(alpha);
                    hasAlpha = true;
                }
                const bitstream = this.#chunks.find(([header]) => header === "VP8 " /* ChunkHeader.VP8 */ || header === "VP8L" /* ChunkHeader.VP8L */);
                if (bitstream === undefined) {
                    throw new Error("Still image does not contain any bitstream subchunks.");
                }
                imageData.push(bitstream);
            }
            else {
                imageData.push(anim);
                const frames = this.#chunks.filter(([header]) => header === "ANMF" /* ChunkHeader.ANMF */);
                if (frames.length === 0) {
                    throw new Error("Animated image contains no frames.");
                }
                for (const chunk of frames) {
                    imageData.push(chunk);
                }
            }
            const xmp = this.#chunks.find(([header]) => header === "XMP " /* ChunkHeader.XMP */);
            const unknownChunks = this.#chunks.filter(([header]) => typeof header === "number");
            // Calculate the size of the total image by summing up the chunks and the
            // size of the exif data.
            // The `RIFF` header as well as the length itself is not part of the length
            // which is why the header is only counted as 22 bytes, igoring the 8 bytes
            // at the start.
            const riffHeaderLength = 22;
            const chunkHeaderLength = 8;
            let length = riffHeaderLength;
            if (iccp !== undefined) {
                const paddingByte = iccp[2] % 2;
                length += chunkHeaderLength + iccp[2] + paddingByte;
            }
            length += imageData.reduce((acc, chunk) => {
                const paddingByte = chunk[2] % 2;
                return acc + chunkHeaderLength + chunk[2] + paddingByte;
            }, 0);
            length += unknownChunks.reduce((acc, chunk) => {
                const paddingByte = chunk[2] % 2;
                return acc + chunkHeaderLength + chunk[2] + paddingByte;
            }, 0);
            if (exif.byteLength !== 0) {
                const paddingByte = exif.byteLength % 2;
                length += chunkHeaderLength + exif.byteLength + paddingByte;
            }
            if (xmp !== undefined) {
                const paddingByte = xmp[2] % 2;
                length += chunkHeaderLength + xmp[2] + paddingByte;
            }
            // The 8 bytes for the `RIFF` header plus the chunk length are not part of
            // `length.`.
            const totalFileSize = length + 8;
            const result = new Uint8Array(totalFileSize);
            const dataView = new DataView(result.buffer, result.byteOffset, result.byteLength);
            dataView.setUint32(0, 0x52494646); // RIFF
            dataView.setUint32(4, length, true);
            dataView.setUint32(8, 0x57454250); // WEBP
            dataView.setUint32(12, 0x56503858); // VP8X
            dataView.setUint32(16, 10, true);
            dataView.setUint8(20, this.#encodeFlags(iccp !== undefined, hasAlpha, exif.byteLength > 0, false, anim !== undefined));
            // 3 reserved bytes (offset is now 24).
            // width - 1 as uint24LE.
            this.#writeDimension(result, 24, this.width);
            // height - 1 as uint24LE.
            this.#writeDimension(result, 27, this.height);
            let offset = 30;
            if (iccp !== undefined) {
                offset = this.#writeChunk(result, dataView, offset, iccp[0], new Uint8Array(this.#buffer.slice(iccp[1], iccp[1] + iccp[2])));
            }
            for (const chunk of imageData) {
                offset = this.#writeChunk(result, dataView, offset, chunk[0], new Uint8Array(this.#buffer.slice(chunk[1], chunk[1] + chunk[2])));
            }
            if (exif.byteLength > 0) {
                offset = this.#writeChunk(result, dataView, offset, "EXIF" /* ChunkHeader.EXIF */, exif);
            }
            if (xmp !== undefined) {
                offset = this.#writeChunk(result, dataView, offset, xmp[0], new Uint8Array(this.#buffer.slice(xmp[1], xmp[1] + xmp[2])));
            }
            for (const chunk of unknownChunks) {
                offset = this.#writeChunk(result, dataView, offset, chunk[0], new Uint8Array(this.#buffer.slice(chunk[1], chunk[1] + chunk[2])));
            }
            if (offset !== totalFileSize) {
                throw new Error(`Encoding failed, only ${offset} of ${totalFileSize} bytes have been written.`);
            }
            return result;
        }
        get height() {
            return this.#height;
        }
        get width() {
            return this.#width;
        }
        #writeDimension(result, offset, value) {
            const bytes = new Uint8Array(4);
            const dw = new DataView(bytes.buffer, 0, 4);
            // Encode the dimension - 1 as uint32LE
            dw.setUint32(0, value - 1, true);
            // Discards the 4th bit.
            dw.setUint32(0, dw.getUint16(0, true) << (8 + dw.getUint16(2, true)), true);
            result.set(bytes.slice(1, 4), offset);
        }
        #encodeFlags(iccProfile, alpha, exif, xmp, animation) {
            let result = 0;
            // https://developers.google.com/speed/webp/docs/riff_container#extended_file_format
            if (iccProfile) {
                result |= 0b00100000;
            }
            if (alpha) {
                result |= 0b00010000;
            }
            if (exif) {
                result |= 0b00001000;
            }
            if (xmp) {
                result |= 0b00000100;
            }
            if (animation) {
                result |= 0b00000010;
            }
            return result;
        }
        #writeChunk(result, dataView, offset, header, data) {
            header = this.#toFourCC(header);
            dataView.setUint32(offset, header);
            dataView.setUint32(offset + 4, data.byteLength, true);
            result.set(data, offset + 8);
            offset = offset + 8 + data.byteLength;
            if (data.byteLength % 2 === 1) {
                // "If Chunk Size is odd, a single padding byte -- which MUST be 0 to
                // conform with RIFF -- is added."
                dataView.setUint8(offset, 0);
                offset += 1;
            }
            return offset;
        }
        #toFourCC(value) {
            if (typeof value === "number") {
                return value;
            }
            if (value.length !== 4) {
                throw new Error(`Cannot decode "${value}" as FourCC`);
            }
            const buffer = new Uint8Array(4);
            const dataView = new DataView(buffer.buffer, 0, 4);
            for (let i = 0; i < 4; i++) {
                dataView.setUint8(i, value.charCodeAt(i));
            }
            return dataView.getUint32(0);
        }
    }
    function parseVp8x(buffer, dataView) {
        if (dataView.byteLength <= 30) {
            throw new Error("A VP8X encoded WebP must be larger than 30 bytes.");
        }
        // If we reach this point, then we have already consumed the first 20 bytes of
        // the buffer. (offset = 20)
        // The next 8 bits contain the flags. (offset + 1 = 21)
        // The next 24 bits are reserved. (offset + 3 = 24)
        // The next 48 bits contain the width and height, represented as uint24LE, but
        // using the value - 1, thus we need to add 1 to each calculated value.
        const width = ((dataView.getUint8(26) << 16) | (dataView.getUint8(25) << 8) | dataView.getUint8(24)) + 1;
        const height = ((dataView.getUint8(29) << 16) | (dataView.getUint8(28) << 8) | dataView.getUint8(27)) + 1;
        const chunks = [];
        let offset = 30;
        while (offset < dataView.byteLength) {
            const chunkHeader = decodeHeader(dataView.getUint32(offset));
            const chunkSize = dataView.getUint32(offset + 4, true);
            offset += 8;
            chunks.push([chunkHeader, offset, chunkSize]);
            offset += chunkSize;
            if (chunkSize % 2 === 1) {
                // "If Chunk Size is odd, a single padding byte -- which MUST be 0 to
                // conform with RIFF -- is added."
                offset += 1;
            }
            if (offset > dataView.byteLength) {
                const header = typeof chunkHeader === "number" ? `0x${chunkHeader.toString(16)}` : chunkHeader;
                throw new Error(`Corrupted image detected, offset ${offset} > ${dataView.byteLength} for chunk ${header}.`);
            }
        }
        return new WebP(buffer, width, height, chunks);
    }
    function getDimensions(buffer) {
        // This is the lazy version that avoids having to implement an RFC 6386 parser
        // to extract the dimensions from the VP8/VP8L frames.
        const blob = new Blob([new Uint8Array(buffer)], { type: "image/webp" });
        const img = new Image();
        img.src = window.URL.createObjectURL(blob);
        return [img.naturalWidth, img.naturalHeight];
    }
    function parseWebPFromBuffer(buffer) {
        const dataView = new DataView(buffer, 0, buffer.byteLength);
        if (dataView.byteLength < 20) {
            // Anything below 20 bytes cannot be an WebP image. The first 12 bytes are
            // the RIFF header followed by at least 8 bytes for a chunk plus its size.
            return undefined;
        }
        if (decodeHeader(dataView.getUint32(0)) !== "RIFF" /* ChunkHeader.RIFF */) {
            return undefined;
        }
        // The next 4 bytes represent the total size of the file.
        if (decodeHeader(dataView.getUint32(8)) !== "WEBP" /* ChunkHeader.WEBP */) {
            return undefined;
        }
        const firstChunk = decodeHeader(dataView.getUint32(12));
        if (typeof firstChunk === "number") {
            // The first chunk must be a known value.
            throw new Error(`Unrecognized chunk 0x${firstChunk.toString(16)} at the first position`);
        }
        const chunkSize = dataView.getUint32(16, true);
        switch (firstChunk) {
            case "VP8 " /* ChunkHeader.VP8 */:
            case "VP8L" /* ChunkHeader.VP8L */: {
                const [width, height] = getDimensions(buffer);
                return new WebP(buffer, width, height, [[firstChunk, 20, chunkSize]]);
            }
            case "VP8X" /* ChunkHeader.VP8X */:
                return parseVp8x(buffer, dataView);
            default:
                throw new Error(`Unexpected chunk "${firstChunk}" at the first position`);
        }
    }
});
