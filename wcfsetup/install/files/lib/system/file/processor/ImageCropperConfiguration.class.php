<?php

namespace wcf\system\file\processor;

final class ImageCropperConfiguration implements \JsonSerializable
{
    public readonly float $aspectRatio;

    /**
     * @var ImageCropSize[]
     */
    public readonly array $sizes;

    public function __construct(
        public readonly ImageCropperType $type,
        ImageCropSize ...$sizes
    ) {
        if ($sizes === []) {
            throw new \InvalidArgumentException('At least one size must be provided.');
        }

        $size = $sizes[0];
        $this->aspectRatio = $size->aspectRatio();

        foreach ($sizes as $size) {
            if ($size->aspectRatio() !== $this->aspectRatio) {
                throw new \InvalidArgumentException('All sizes must have the same aspect ratio.');
            }
        }

        \usort($sizes, function (ImageCropSize $a, ImageCropSize $b) {
            if ($a->width > $a->height) {
                return $a->width <=> $b->width;
            } else {
                return $a->height <=> $b->height;
            }
        });
        $this->sizes = $sizes;
    }

    #[\Override]
    public function jsonSerialize(): mixed
    {
        return [
            'aspectRatio' => $this->aspectRatio,
            'sizes' => $this->sizes,
            'type' => $this->type->toString(),
        ];
    }

    /**
     * Creates an image cropper with minimum and maximum size with the same aspect ratio.
     * The user can freely select, move and scale.
     * However, the cropping area is limited to `$min` and `$max`.
     */
    public static function createMinMax(ImageCropSize $min, ImageCropSize $max): self
    {
        return new self(ImageCropperType::MinMax, $min, $max);
    }

    /**
     * Creates an image cropper that reduces the image to a specific size
     * and only allows the user to move the cropping area.
     * The size is determined by `$sizes` and corresponds to the smallest side of the image that is the next smaller
     * or equal size of `$sizes`. The aspect ratio of the uploaded image is retained.
     *
     * Example:
     * `$sizes` is [128x128, 256x256]
     * - Image is 100x200
     *   - Image is rejected
     * - Image is 200x150
     *   - Image is resized to 170x128
     * - Image is 150x200
     *   - Image is resized to 128x170
     * - Image is 300x300
     *   - Image is resized to 256x256
     */
    public static function createExact(ImageCropSize ...$sizes): self
    {
        return new self(ImageCropperType::Exact, ...$sizes);
    }
}
