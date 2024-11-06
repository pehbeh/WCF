import ImageResizer from "WoltLabSuite/Core/Image/Resizer";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import Cropper, { CropperCanvas, CropperImage, CropperSelection } from "cropperjs";
import type { Selection } from "@cropper/element-selection";
import { getPhrase } from "WoltLabSuite/Core/Language";
import WoltlabCoreDialogElement from "WoltLabSuite/Core/Element/woltlab-core-dialog";
import * as ExifUtil from "WoltLabSuite/Core/Image/ExifUtil";

export interface CropperConfiguration {
  aspectRatio: number;
  type: "minMax" | "exact";
  sizes: {
    width: number;
    height: number;
  }[];
}

abstract class ImageCropper {
  readonly configuration: CropperConfiguration;
  readonly file: File;
  readonly element: WoltlabCoreFileUploadElement;
  readonly resizer: ImageResizer;
  protected image?: HTMLImageElement | HTMLCanvasElement;
  protected cropperCanvas?: CropperCanvas | null;
  protected cropperImage?: CropperImage | null;
  protected cropperSelection?: CropperSelection | null;
  protected dialog?: WoltlabCoreDialogElement;
  protected exif?: ExifUtil.Exif;
  #cropper?: Cropper;

  constructor(element: WoltlabCoreFileUploadElement, file: File, configuration: CropperConfiguration) {
    this.configuration = configuration;
    this.element = element;
    this.file = file;
    this.resizer = new ImageResizer();

    this.configuration.sizes = this.configuration.sizes.sort((a, b) => {
      if (a.width >= a.height) {
        return b.width - a.width;
      } else {
        return b.height - a.height;
      }
    });
  }

  public async showDialog(): Promise<File> {
    this.dialog = dialogFactory().fromElement(this.image!).asPrompt({
      extra: this.getDialogExtra(),
    });
    this.dialog.show(getPhrase("wcf.upload.crop.image"));

    this.createCropper();

    return new Promise<File>((resolve, reject) => {
      this.dialog!.addEventListener("primary", () => {
        this.cropperSelection!.$toCanvas()
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

  public async loadImage() {
    const { image, exif } = await this.resizer.loadFile(this.file);
    this.image = image;
    this.exif = exif;
  }

  protected setCropperStyle() {
    this.cropperCanvas!.style.aspectRatio = `${this.image!.width}/${this.image!.height}`;

    if (this.image!.width > this.image!.height) {
      this.cropperCanvas!.style.width = `min(70vw, ${this.image!.width}px)`;
      this.cropperCanvas!.style.height = "auto";
    } else {
      this.cropperCanvas!.style.height = `min(60vh, ${this.image!.height}px)`;
      this.cropperCanvas!.style.width = "auto";
    }

    this.cropperSelection!.aspectRatio = this.configuration.aspectRatio;
  }

  protected abstract getCropperTemplate(): string;

  protected getDialogExtra(): string | undefined {
    return undefined;
  }

  protected createCropper() {
    this.#cropper = new Cropper(this.image!, {
      template: this.getCropperTemplate(),
    });

    this.cropperCanvas = this.#cropper.getCropperCanvas();
    this.cropperImage = this.#cropper.getCropperImage();
    this.cropperSelection = this.#cropper.getCropperSelection();

    this.setCropperStyle();

    this.cropperImage!.$center("contain");
    this.cropperSelection!.$center();

    // Limit the selection to the canvas boundaries
    this.cropperSelection!.addEventListener("change", (event: CustomEvent) => {
      // see https://fengyuanchen.github.io/cropperjs/v2/api/cropper-selection.html#limit-boundaries
      const cropperCanvasRect = this.cropperCanvas!.getBoundingClientRect();
      const selection = event.detail as Selection;

      const maxSelection: Selection = {
        x: 0,
        y: 0,
        width: cropperCanvasRect.width,
        height: cropperCanvasRect.height,
      };

      if (
        selection.x < maxSelection.x ||
        selection.y < maxSelection.y ||
        selection.x + selection.width > maxSelection.x + maxSelection.width ||
        selection.y + selection.height > maxSelection.y + maxSelection.height
      ) {
        event.preventDefault();
      }
    });
  }
}

class ExactImageCropper extends ImageCropper {
  #size?: { width: number; height: number };

  public async showDialog(): Promise<File> {
    // The image already has the correct size, cropping is not necessary
    if (
      this.image!.width == this.#size!.width &&
      this.image!.height == this.#size!.height &&
      this.image instanceof HTMLCanvasElement
    ) {
      return this.resizer.saveFile({ exif: this.exif, image: this.image }, this.file.name, this.file.type);
    }

    return super.showDialog();
  }

  public async loadImage(): Promise<void> {
    await super.loadImage();

    const timeout = new Promise<File>((resolve) => {
      window.setTimeout(() => resolve(this.file), 10_000);
    });

    // resize image to the largest possible size
    const sizes = this.configuration.sizes.filter((size) => {
      return size.width <= this.image!.width && size.height <= this.image!.height;
    });

    if (sizes.length === 0) {
      const smallestSize =
        this.configuration.sizes.length > 1 ? this.configuration.sizes[this.configuration.sizes.length - 1] : undefined;
      throw new Error(
        getPhrase("wcf.upload.error.image.tooSmall", {
          width: smallestSize?.width,
          height: smallestSize?.height,
        }),
      );
    }

    this.#size = sizes[0];
    this.image = await this.resizer.resize(
      this.image as HTMLImageElement,
      this.image!.width >= this.image!.height ? this.image!.width : this.#size.width,
      this.image!.height > this.image!.width ? this.image!.height : this.#size.height,
      this.resizer.quality,
      true,
      timeout,
    );
  }

  protected getCropperTemplate(): string {
    return `<div class="cropperContainer">
  <cropper-canvas background>
    <cropper-image></cropper-image>
    <cropper-shade hidden></cropper-shade>
    <cropper-selection movable outlined keyboard>
      <cropper-grid role="grid" bordered covered></cropper-grid>
      <cropper-crosshair centered></cropper-crosshair>
      <cropper-handle action="move" theme-color="rgba(255, 255, 255, 0.35)"></cropper-handle>
    </cropper-selection>
  </cropper-canvas>
</div>`;
  }

  protected setCropperStyle() {
    super.setCropperStyle();

    this.cropperSelection!.width = this.#size!.width;
    this.cropperSelection!.height = this.#size!.height;

    this.cropperCanvas!.style.width = `${this.image!.width}px`;
    this.cropperCanvas!.style.height = `${this.image!.height}px`;
    this.cropperSelection!.style.removeProperty("aspectRatio");
  }
}

class MinMaxImageCropper extends ImageCropper {
  constructor(element: WoltlabCoreFileUploadElement, file: File, configuration: CropperConfiguration) {
    super(element, file, configuration);
    if (configuration.sizes.length !== 2) {
      throw new Error("MinMaxImageCropper requires exactly two sizes");
    }
  }

  get minSize() {
    return this.configuration.sizes[1];
  }

  get maxSize() {
    return this.configuration.sizes[0];
  }

  protected getDialogExtra(): string {
    return getPhrase("wcf.global.button.reset");
  }

  protected getCropperTemplate(): string {
    return `<div class="cropperContainer">
  <cropper-canvas background>
    <cropper-image skewable scalable translatable></cropper-image>
    <cropper-shade hidden></cropper-shade>
    <cropper-handle action="move" plain></cropper-handle>
    <cropper-selection movable zoomable resizable outlined>
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
  </cropper-canvas>
</div>`;
  }

  protected setCropperStyle() {
    super.setCropperStyle();

    this.cropperSelection!.width = this.minSize.width;
    this.cropperSelection!.height = this.minSize.height;
    this.cropperCanvas!.style.minWidth = `min(${this.maxSize.width}px, ${this.image!.width}px)`;
    this.cropperCanvas!.style.minHeight = `min(${this.maxSize.height}px, ${this.image!.height}px)`;
  }

  protected createCropper() {
    super.createCropper();

    this.dialog!.addEventListener("extra", () => {
      this.cropperImage!.$center("contain");
      this.cropperSelection!.$reset();
    });

    // Limit the selection to the canvas boundaries
    this.cropperSelection!.addEventListener("change", (event: CustomEvent) => {
      const selection = event.detail as Selection;

      if (
        selection.width < this.minSize.width ||
        selection.height < this.minSize.height ||
        selection.width > this.maxSize.width ||
        selection.height > this.maxSize.height
      ) {
        event.preventDefault();
      }
    });
  }
}

export async function cropImage(
  element: WoltlabCoreFileUploadElement,
  file: File,
  configuration: CropperConfiguration,
): Promise<File> {
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

  let imageCropper: ImageCropper;
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
