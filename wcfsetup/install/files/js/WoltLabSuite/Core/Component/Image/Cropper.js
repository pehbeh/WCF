define(["require", "exports", "tslib", "WoltLabSuite/Core/Image/Resizer", "WoltLabSuite/Core/Component/Dialog", "cropperjs", "WoltLabSuite/Core/Language"], function (require, exports, tslib_1, Resizer_1, Dialog_1, cropperjs_1, Language_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.cropImage = cropImage;
    Resizer_1 = tslib_1.__importDefault(Resizer_1);
    cropperjs_1 = tslib_1.__importDefault(cropperjs_1);
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
        #cropper;
        constructor(element, file, configuration) {
            this.configuration = configuration;
            this.element = element;
            this.file = file;
            this.resizer = new Resizer_1.default();
        }
        async showDialog() {
            await this.loadImage();
            this.dialog = (0, Dialog_1.dialogFactory)().fromElement(this.image).asPrompt({
                extra: this.getDialogExtra(),
            });
            this.dialog.show((0, Language_1.getPhrase)("wcf.upload.crop.image"));
            return this.createCropper();
        }
        async createCropper() {
            this.#cropper = new cropperjs_1.default(this.image, {
                template: this.getCropperTemplate(),
            });
            this.cropperCanvas = this.#cropper.getCropperCanvas();
            this.cropperImage = this.#cropper.getCropperImage();
            this.cropperSelection = this.#cropper.getCropperSelection();
            this.setCropperStyle();
            this.cropperImage.$center("contain");
            this.cropperSelection.$center();
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
                if (selection.x < maxSelection.x ||
                    selection.y < maxSelection.y ||
                    selection.x + selection.width > maxSelection.x + maxSelection.width ||
                    selection.y + selection.height > maxSelection.y + maxSelection.height) {
                    event.preventDefault();
                }
            });
            this.dialog.addEventListener("extra", () => {
                this.cropperImage.$center("contain");
                this.cropperSelection.$reset();
            });
            return new Promise((resolve, reject) => {
                this.dialog.addEventListener("primary", () => {
                    this.cropperSelection.$toCanvas()
                        .then((canvas) => {
                        this.resizer
                            .saveFile({ exif: this.exif, image: canvas }, this.file.name, this.file.type)
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
        setCropperStyle() {
            this.cropperCanvas.style.aspectRatio = `${this.image.width}/${this.image.height}`;
            if (this.image.width > this.image.height) {
                this.cropperCanvas.style.width = `min(70vw, ${this.image.width}px)`;
                this.cropperCanvas.style.height = "auto";
            }
            else {
                this.cropperCanvas.style.height = `min(60vh, ${this.image.height}px)`;
                this.cropperCanvas.style.width = "auto";
            }
            this.cropperSelection.aspectRatio = this.configuration.aspectRatio;
        }
        getDialogExtra() {
            return undefined;
        }
        async loadImage() {
            const { image, exif } = await this.resizer.loadFile(this.file);
            this.image = image;
            this.exif = exif;
        }
    }
    class ExactImageCropper extends ImageCropper {
        #size;
        async showDialog() {
            await this.loadImage();
            // The image already has the correct size, cropping is not necessary
            if (this.image.width == this.#size.width &&
                this.image.height == this.#size.height &&
                this.image instanceof HTMLCanvasElement) {
                return this.resizer.saveFile({ exif: this.exif, image: this.image }, this.file.name, this.file.type);
            }
            this.dialog = (0, Dialog_1.dialogFactory)().fromElement(this.image).asPrompt({
                extra: this.getDialogExtra(),
            });
            this.dialog.show((0, Language_1.getPhrase)("wcf.upload.crop.image"));
            return this.createCropper();
        }
        getCropperTemplate() {
            return `<cropper-canvas background>
  <cropper-image></cropper-image>
  <cropper-shade hidden></cropper-shade>
  <cropper-selection movable outlined keyboard>
    <cropper-grid role="grid" bordered covered></cropper-grid>
    <cropper-crosshair centered></cropper-crosshair>
    <cropper-handle action="move" theme-color="rgba(255, 255, 255, 0.35)"></cropper-handle>
  </cropper-selection>
</cropper-canvas>`;
        }
        async loadImage() {
            await super.loadImage();
            const timeout = new Promise((resolve) => {
                window.setTimeout(() => resolve(this.file), 10_000);
            });
            // resize image to the largest possible size
            this.configuration.sizes = this.configuration.sizes.sort((a, b) => {
                if (a.width >= a.height) {
                    return b.width - a.width;
                }
                else {
                    return b.height - a.height;
                }
            });
            const sizes = this.configuration.sizes.filter((size) => {
                return size.width <= this.image.width && size.height <= this.image.height;
            });
            if (sizes.length === 0) {
                const smallestSize = this.configuration.sizes.length > 1 ? this.configuration.sizes[this.configuration.sizes.length - 1] : undefined;
                throw new Error((0, Language_1.getPhrase)("wcf.upload.error.image.tooSmall", {
                    width: smallestSize?.width,
                    height: smallestSize?.height,
                }));
            }
            this.#size = sizes[0];
            this.image = await this.resizer.resize(this.image, this.image.width >= this.image.height ? this.image.width : this.#size.width, this.image.height > this.image.width ? this.image.height : this.#size.height, this.resizer.quality, true, timeout);
        }
        setCropperStyle() {
            super.setCropperStyle();
            this.cropperSelection.width = this.#size.width;
            this.cropperSelection.height = this.#size.height;
        }
    }
    class MinMaxImageCropper extends ImageCropper {
        getCropperTemplate() {
            return `<cropper-canvas background>
  <cropper-image skewable scalable translatable></cropper-image>
  <cropper-shade hidden></cropper-shade>
  <cropper-handle action="move" plain></cropper-handle>
  <cropper-selection initial-coverage="0.5" movable resizable outlined>
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
        getDialogExtra() {
            return (0, Language_1.getPhrase)("wcf.global.button.reset");
        }
    }
    async function cropImage(element, file, configuration) {
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
        return imageCropper.showDialog();
    }
});
