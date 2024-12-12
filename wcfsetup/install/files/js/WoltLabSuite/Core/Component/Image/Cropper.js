/**
 * An image cropper that allows the user to crop an image before uploading it.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Image/Resizer", "WoltLabSuite/Core/Component/Dialog", "cropperjs", "WoltLabSuite/Core/Language", "exifreader", "WoltLabSuite/Core/Dom/Util"], function (require, exports, tslib_1, Resizer_1, Dialog_1, cropperjs_1, Language_1, exifreader_1, Util_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.cropImage = cropImage;
    Resizer_1 = tslib_1.__importDefault(Resizer_1);
    cropperjs_1 = tslib_1.__importDefault(cropperjs_1);
    exifreader_1 = tslib_1.__importDefault(exifreader_1);
    Util_1 = tslib_1.__importDefault(Util_1);
    function inSelection(selection, maxSelection) {
        return (Math.round(selection.x) >= maxSelection.x &&
            Math.round(selection.y) >= maxSelection.y &&
            Math.round(selection.x + selection.width) <= Math.round(maxSelection.x + maxSelection.width) &&
            Math.round(selection.y + selection.height) <= Math.round(maxSelection.y + maxSelection.height));
    }
    class ImageCropper {
        configuration;
        file;
        element;
        resizer;
        image;
        cropperCanvas;
        cropperImage;
        cropperSelection;
        dialog;
        exif;
        orientation;
        #cropper;
        constructor(element, file, configuration) {
            this.configuration = configuration;
            this.element = element;
            this.file = file;
            this.resizer = new Resizer_1.default();
        }
        get width() {
            switch (this.orientation) {
                case 90:
                case 270:
                    return this.image.height;
                default:
                    return this.image.width;
            }
        }
        get height() {
            switch (this.orientation) {
                case 90:
                case 270:
                    return this.image.width;
                default:
                    return this.image.height;
            }
        }
        async showDialog() {
            this.dialog = (0, Dialog_1.dialogFactory)().fromElement(this.image).asPrompt({
                extra: this.getDialogExtra(),
            });
            this.dialog.show((0, Language_1.getPhrase)("wcf.upload.crop.image"));
            this.createCropper();
            const resize = () => {
                this.centerSelection();
            };
            window.addEventListener("resize", resize, { passive: true });
            this.dialog.addEventListener("afterClose", () => {
                window.removeEventListener("resize", resize);
            }, {
                once: true,
            });
            return new Promise((resolve, reject) => {
                this.dialog.addEventListener("primary", () => {
                    void this.getCanvas()
                        .then((canvas) => {
                        this.resizer
                            .saveFile({ exif: this.orientation ? undefined : this.exif, image: canvas }, this.file.name, this.file.type)
                            .then((resizedFile) => {
                            resolve(resizedFile);
                        })
                            .catch(() => {
                            reject();
                        });
                    })
                        .catch(() => {
                        reject();
                    });
                });
            });
        }
        getCanvas() {
            return this.cropperSelection.$toCanvas();
        }
        async loadImage() {
            const { image, exif } = await this.resizer.loadFile(this.file);
            this.image = image;
            this.exif = exif;
            const tags = await exifreader_1.default.load(this.file);
            if (tags.Orientation) {
                switch (tags.Orientation.value) {
                    case 3:
                        this.orientation = 180;
                        break;
                    case 6:
                        this.orientation = 90;
                        break;
                    case 8:
                        this.orientation = 270;
                        break;
                    // Any other rotation is unsupported.
                }
            }
        }
        getDialogExtra() {
            return undefined;
        }
        setCropperStyle() {
            this.cropperCanvas.style.aspectRatio = `${this.width}/${this.height}`;
            if (this.width >= this.height) {
                this.cropperCanvas.style.maxHeight = "100%";
            }
            else {
                this.cropperCanvas.style.maxWidth = "100%";
            }
            this.cropperSelection.aspectRatio = this.configuration.aspectRatio;
        }
        createCropper() {
            this.#cropper = new cropperjs_1.default(this.image, {
                template: this.getCropperTemplate(),
            });
            this.cropperCanvas = this.#cropper.getCropperCanvas();
            this.cropperImage = this.#cropper.getCropperImage();
            this.cropperSelection = this.#cropper.getCropperSelection();
            this.setCropperStyle();
            if (this.orientation) {
                this.cropperImage.$rotate(`${this.orientation}deg`);
            }
            this.centerSelection();
            // Limit the selection to the canvas boundaries
            this.cropperSelection.addEventListener("change", (event) => {
                // see https://fengyuanchen.github.io/cropperjs/v2/api/cropper-selection.html#limit-boundaries
                const cropperCanvasRect = this.cropperCanvas.getBoundingClientRect();
                const selection = event.detail;
                const maxSelection = {
                    x: 0,
                    y: 0,
                    width: cropperCanvasRect.width,
                    height: cropperCanvasRect.height,
                };
                if (!inSelection(selection, maxSelection)) {
                    event.preventDefault();
                }
            });
        }
        centerSelection() {
            this.cropperImage.$center("contain");
            this.cropperSelection.$center();
            this.cropperSelection.scrollIntoView({ block: "center", inline: "center" });
        }
    }
    class ExactImageCropper extends ImageCropper {
        #size;
        async showDialog() {
            // The image already has the correct size, cropping is not necessary
            if (this.width == this.#size.width &&
                this.height == this.#size.height &&
                this.image instanceof HTMLCanvasElement) {
                return this.resizer.saveFile({ exif: this.orientation ? undefined : this.exif, image: this.image }, this.file.name, this.file.type);
            }
            return super.showDialog();
        }
        async loadImage() {
            await super.loadImage();
            const timeout = new Promise((resolve) => {
                window.setTimeout(() => resolve(this.file), 10_000);
            });
            // resize image to the largest possible size
            const sizes = this.configuration.sizes.filter((size) => {
                return size.width <= this.width && size.height <= this.height;
            });
            if (sizes.length === 0) {
                const smallestSize = this.configuration.sizes.length > 1 ? this.configuration.sizes[this.configuration.sizes.length - 1] : undefined;
                throw new Error((0, Language_1.getPhrase)("wcf.upload.error.image.tooSmall", {
                    width: smallestSize?.width,
                    height: smallestSize?.height,
                }));
            }
            this.#size = sizes[sizes.length - 1];
            this.image = await this.resizer.resize(this.image, this.width >= this.height ? this.width : this.#size.width, this.height > this.width ? this.height : this.#size.height, this.resizer.quality, true, timeout);
        }
        getCropperTemplate() {
            return `<cropper-canvas background>
  <cropper-image rotatable></cropper-image>
  <cropper-shade hidden></cropper-shade>
  <cropper-selection movable outlined keyboard>
    <cropper-grid role="grid" bordered covered></cropper-grid>
    <cropper-crosshair centered></cropper-crosshair>
    <cropper-handle action="move" theme-color="rgba(255, 255, 255, 0.35)"></cropper-handle>
  </cropper-selection>
</cropper-canvas>`;
        }
        setCropperStyle() {
            super.setCropperStyle();
            this.cropperSelection.width = this.#size.width;
            this.cropperSelection.height = this.#size.height;
            this.cropperCanvas.style.width = `${this.width}px`;
            this.cropperCanvas.style.height = `${this.height}px`;
            this.cropperSelection.style.removeProperty("aspectRatio");
        }
    }
    class MinMaxImageCropper extends ImageCropper {
        #cropperCanvasRect;
        constructor(element, file, configuration) {
            super(element, file, configuration);
            if (configuration.sizes.length !== 2) {
                throw new Error("MinMaxImageCropper requires exactly two sizes");
            }
        }
        get minSize() {
            return this.configuration.sizes[0];
        }
        get maxSize() {
            return this.configuration.sizes[1];
        }
        getDialogExtra() {
            return (0, Language_1.getPhrase)("wcf.global.button.reset");
        }
        async loadImage() {
            await super.loadImage();
            if (this.image.width < this.minSize.width || this.image.height < this.minSize.height) {
                throw new Error((0, Language_1.getPhrase)("wcf.upload.error.image.tooSmall", {
                    width: this.minSize.width,
                    height: this.minSize.height,
                }));
            }
        }
        getCropperTemplate() {
            return `<cropper-canvas background scale-step="0.0">
  <cropper-image skewable scalable translatable rotatable></cropper-image>
  <cropper-shade hidden></cropper-shade>
  <cropper-handle action="scale" hidden disabled></cropper-handle>
  <cropper-selection precise movable resizable outlined>
    <cropper-grid role="grid" bordered covered></cropper-grid>
    <cropper-crosshair centered></cropper-crosshair>
    <cropper-handle action="move" theme-color="rgba(255, 255, 255, 0.35)"></cropper-handle>
    <cropper-handle action="n-resize"></cropper-handle>
    <cropper-handle action="e-resize"></cropper-handle>
    <cropper-handle action="s-resize"></cropper-handle>
    <cropper-handle action="w-resize"></cropper-handle>
    <cropper-handle action="ne-resize"></cropper-handle>
    <cropper-handle action="nw-resize"></cropper-handle>
    <cropper-handle action="se-resize"></cropper-handle>
    <cropper-handle action="sw-resize"></cropper-handle>
  </cropper-selection>
</cropper-canvas>`;
        }
        createCropper() {
            super.createCropper();
            this.dialog.addEventListener("extra", () => {
                this.centerSelection();
            });
            // Limit the selection to the min/max size
            this.cropperSelection.addEventListener("change", (event) => {
                const selection = event.detail;
                this.#cropperCanvasRect = this.cropperCanvas.getBoundingClientRect();
                const maxImageWidth = Math.min(this.image.width, this.maxSize.width);
                const maxImageHeight = Math.min(this.image.height, this.maxSize.height);
                const selectionRatio = Math.min(this.#cropperCanvasRect.width / maxImageWidth, this.#cropperCanvasRect.height / maxImageHeight);
                const minWidth = this.minSize.width * selectionRatio;
                const maxWidth = this.maxSize.width * selectionRatio;
                const minHeight = minWidth / this.configuration.aspectRatio;
                const maxHeight = maxWidth / this.configuration.aspectRatio;
                if (Math.round(selection.width) < minWidth ||
                    Math.round(selection.height) < minHeight ||
                    Math.round(selection.width) > maxWidth ||
                    Math.round(selection.height) > maxHeight) {
                    event.preventDefault();
                }
            });
        }
        getCanvas() {
            // Calculate the size of the image in relation to the window size
            const maxImageWidth = Math.min(this.image.width, this.maxSize.width);
            const widthRatio = this.#cropperCanvasRect.width / maxImageWidth;
            const width = this.cropperSelection.width / widthRatio;
            const height = width / this.configuration.aspectRatio;
            return this.cropperSelection.$toCanvas({
                width: Math.max(Math.min(Math.ceil(width), this.maxSize.width), this.minSize.width),
                height: Math.max(Math.min(Math.ceil(height), this.maxSize.height), this.minSize.height),
            });
        }
        centerSelection() {
            // Reset to get the maximum available height and width
            this.cropperCanvas.style.height = "";
            this.cropperCanvas.style.width = "";
            const dimension = Util_1.default.innerDimensions(this.cropperCanvas.parentElement);
            const ratio = Math.min(dimension.width / this.image.width, dimension.height / this.image.height);
            this.cropperCanvas.style.height = `${this.image.height * ratio}px`;
            this.cropperCanvas.style.width = `${this.image.width * ratio}px`;
            this.cropperImage.$center("contain");
            this.#cropperCanvasRect = this.cropperImage.getBoundingClientRect();
            const selectionRatio = Math.min(this.#cropperCanvasRect.width / this.maxSize.width, this.#cropperCanvasRect.height / this.maxSize.height);
            this.cropperSelection.$change(0, 0, this.maxSize.width * selectionRatio, this.maxSize.height * selectionRatio, this.configuration.aspectRatio, true);
            this.cropperSelection.$center();
            this.cropperSelection.scrollIntoView({ block: "center", inline: "center" });
        }
    }
    async function cropImage(element, file, configuration) {
        switch (file.type) {
            case "image/jpeg":
            case "image/png":
            case "image/webp":
                // Potential candidate for a resize operation.
                break;
            default:
                // Not an image or an unsupported file type.
                return file;
        }
        let imageCropper;
        switch (configuration.type) {
            case "exact":
                imageCropper = new ExactImageCropper(element, file, configuration);
                break;
            case "minMax":
                imageCropper = new MinMaxImageCropper(element, file, configuration);
                break;
            default:
                throw new Error("Invalid configuration type");
        }
        await imageCropper.loadImage();
        return imageCropper.showDialog();
    }
});
