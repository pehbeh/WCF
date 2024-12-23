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
        cropperCanvasRect;
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
            return new Promise((resolve, reject) => {
                let callReject = true;
                this.dialog.addEventListener("afterClose", () => {
                    window.removeEventListener("resize", resize);
                    // If the dialog is closed without confirming, reject the promise to trigger a cancel event.
                    if (callReject) {
                        reject();
                    }
                });
                this.dialog.addEventListener("primary", () => {
                    callReject = false;
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
        getDialogExtra() {
            return undefined;
        }
        getCanvas() {
            // Calculate the size of the image in relation to the window size
            const selectionRatio = Math.min(this.cropperCanvasRect.width / this.width, this.cropperCanvasRect.height / this.height);
            const width = this.cropperSelection.width / selectionRatio;
            const height = width / this.configuration.aspectRatio;
            return this.cropperSelection.$toCanvas({
                width: Math.max(Math.min(Math.floor(width), this.maxSize.width), this.minSize.width),
                height: Math.max(Math.min(Math.ceil(height), this.maxSize.height), this.minSize.height),
            });
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
            // Limit the selection to the min/max size
            this.cropperSelection.addEventListener("change", (event) => {
                const selection = event.detail;
                this.cropperCanvasRect = this.cropperCanvas.getBoundingClientRect();
                const selectionRatio = Math.min(this.cropperCanvasRect.width / this.width, this.cropperCanvasRect.height / this.height);
                const minWidth = this.minSize.width * selectionRatio;
                const maxWidth = this.cropperCanvasRect.width;
                const minHeight = minWidth / this.configuration.aspectRatio;
                const maxHeight = maxWidth / this.configuration.aspectRatio;
                if (selection.width < minWidth ||
                    selection.height < minHeight ||
                    selection.width > maxWidth ||
                    selection.height > maxHeight) {
                    event.preventDefault();
                }
            });
        }
        setCropperStyle() {
            this.cropperCanvas.style.aspectRatio = `${this.width}/${this.height}`;
            this.cropperSelection.aspectRatio = this.configuration.aspectRatio;
        }
        centerSelection() {
            // Set to the maximum size
            this.cropperCanvas.style.width = `${this.width}px`;
            this.cropperCanvas.style.height = `${this.height}px`;
            const dimension = Util_1.default.innerDimensions(this.cropperCanvas.parentElement);
            const ratio = Math.min(dimension.width / this.width, dimension.height / this.height);
            this.cropperCanvas.style.height = `${this.height * ratio}px`;
            this.cropperCanvas.style.width = `${this.width * ratio}px`;
            this.cropperImage.$center("contain");
            this.cropperCanvasRect = this.cropperImage.getBoundingClientRect();
            const selectionRatio = Math.min(this.cropperCanvasRect.width / this.maxSize.width, this.cropperCanvasRect.height / this.maxSize.height);
            this.cropperSelection.$change(0, 0, Math.min(this.cropperCanvasRect.width, this.maxSize.width * selectionRatio), Math.min(this.cropperCanvasRect.height, this.maxSize.height * selectionRatio), this.configuration.aspectRatio, true);
            this.cropperSelection.$center();
            this.cropperSelection.scrollIntoView({ block: "center", inline: "center" });
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
    }
    class ExactImageCropper extends ImageCropper {
        get minSize() {
            return this.configuration.sizes[0];
        }
        get maxSize() {
            return this.configuration.sizes[this.configuration.sizes.length - 1];
        }
        async showDialog() {
            // The image already has the correct size, cropping is not necessary
            if (this.configuration.sizes.filter((size) => {
                return size.width == this.width && size.height == this.height;
            }).length > 0 &&
                this.image instanceof HTMLCanvasElement) {
                return this.resizer.saveFile({ exif: this.orientation ? undefined : this.exif, image: this.image }, this.file.name, this.file.type);
            }
            return super.showDialog();
        }
        getCanvas() {
            // Calculate the size of the image in relation to the window size
            const selectionRatio = Math.min(this.cropperCanvasRect.width / this.width, this.cropperCanvasRect.height / this.height);
            const width = this.cropperSelection.width / selectionRatio;
            const height = width / this.configuration.aspectRatio;
            const sizes = this.configuration.sizes
                .filter((size) => {
                return width >= size.width && height >= size.height;
            })
                .reverse();
            const size = sizes.length > 0 ? sizes[0] : this.minSize;
            return this.cropperSelection.$toCanvas({
                width: size.width,
                height: size.height,
            });
        }
        async loadImage() {
            await super.loadImage();
            const sizes = this.configuration.sizes
                .filter((size) => {
                return size.width <= this.width && size.height <= this.height;
            })
                .sort((a, b) => {
                if (this.configuration.aspectRatio >= 1) {
                    return a.width - b.width;
                }
                else {
                    return a.height - b.height;
                }
            });
            if (sizes.length === 0) {
                const smallestSize = this.configuration.sizes.length > 1 ? this.configuration.sizes[this.configuration.sizes.length - 1] : undefined;
                throw new Error((0, Language_1.getPhrase)("wcf.upload.error.image.tooSmall", {
                    width: smallestSize?.width,
                    height: smallestSize?.height,
                }));
            }
            this.configuration.sizes = sizes;
        }
    }
    class MinMaxImageCropper extends ImageCropper {
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
            if (this.width < this.minSize.width || this.height < this.minSize.height) {
                throw new Error((0, Language_1.getPhrase)("wcf.upload.error.image.tooSmall", {
                    width: this.minSize.width,
                    height: this.minSize.height,
                }));
            }
        }
        createCropper() {
            super.createCropper();
            this.dialog.addEventListener("extra", () => {
                this.centerSelection();
            });
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
