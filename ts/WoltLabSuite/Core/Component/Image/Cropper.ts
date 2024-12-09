/**
 * An image cropper that allows the user to crop an image before uploading it.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 */

import ImageResizer from "WoltLabSuite/Core/Image/Resizer";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import Cropper, { CropperCanvas, CropperImage, CropperSelection } from "cropperjs";
import type { Selection } from "@cropper/element-selection";
import { getPhrase } from "WoltLabSuite/Core/Language";
import WoltlabCoreDialogElement from "WoltLabSuite/Core/Element/woltlab-core-dialog";
import * as ExifUtil from "WoltLabSuite/Core/Image/ExifUtil";
import ExifReader from "exifreader";
import DomUtil from "WoltLabSuite/Core/Dom/Util";

export interface CropperConfiguration {
  aspectRatio: number;
  type: "minMax" | "exact";
  sizes: {
    width: number;
    height: number;
  }[];
}

function inSelection(selection: Selection, maxSelection: Selection): boolean {
  return (
    Math.ceil(selection.x) >= maxSelection.x &&
    Math.ceil(selection.y) >= maxSelection.y &&
    Math.ceil(selection.x + selection.width) <= Math.ceil(maxSelection.x + maxSelection.width) &&
    Math.ceil(selection.y + selection.height) <= Math.ceil(maxSelection.y + maxSelection.height)
  );
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
  protected orientation?: number;
  #cropper?: Cropper;

  constructor(element: WoltlabCoreFileUploadElement, file: File, configuration: CropperConfiguration) {
    this.configuration = configuration;
    this.element = element;
    this.file = file;
    this.resizer = new ImageResizer();
  }

  protected get width() {
    switch (this.orientation) {
      case 90:
      case 270:
        return this.image!.height;
      default:
        return this.image!.width;
    }
  }

  protected get height() {
    switch (this.orientation) {
      case 90:
      case 270:
        return this.image!.width;
      default:
        return this.image!.height;
    }
  }

  public async showDialog(): Promise<File> {
    this.dialog = dialogFactory().fromElement(this.image!).asPrompt({
      extra: this.getDialogExtra(),
    });
    this.dialog.show(getPhrase("wcf.upload.crop.image"));

    this.createCropper();

    const resize = () => {
      this.centerSelection();
    };

    window.addEventListener("resize", resize, { passive: true });
    this.dialog.addEventListener(
      "afterClose",
      () => {
        window.removeEventListener("resize", resize);
      },
      {
        once: true,
      },
    );

    return new Promise<File>((resolve, reject) => {
      this.dialog!.addEventListener("primary", () => {
        void this.getCanvas()
          .then((canvas) => {
            this.resizer
              .saveFile(
                { exif: this.orientation ? undefined : this.exif, image: canvas },
                this.file.name,
                this.file.type,
              )
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

  protected getCanvas(): Promise<HTMLCanvasElement> {
    return this.cropperSelection!.$toCanvas();
  }

  public async loadImage() {
    const { image, exif } = await this.resizer.loadFile(this.file);
    this.image = image;
    this.exif = exif;
    const tags = await ExifReader.load(this.file);
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

  protected abstract getCropperTemplate(): string;

  protected getDialogExtra(): string | undefined {
    return undefined;
  }

  protected setCropperStyle() {
    this.cropperCanvas!.style.aspectRatio = `${this.width}/${this.height}`;

    if (this.width >= this.height) {
      this.cropperCanvas!.style.maxHeight = "100%";
    } else {
      this.cropperCanvas!.style.maxWidth = "100%";
    }

    this.cropperSelection!.aspectRatio = this.configuration.aspectRatio;
  }

  protected createCropper() {
    this.#cropper = new Cropper(this.image!, {
      template: this.getCropperTemplate(),
    });

    this.cropperCanvas = this.#cropper.getCropperCanvas();
    this.cropperImage = this.#cropper.getCropperImage();
    this.cropperSelection = this.#cropper.getCropperSelection();

    this.setCropperStyle();

    if (this.orientation) {
      this.cropperImage!.$rotate(`${this.orientation}deg`);
    }

    this.centerSelection();

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

      if (!inSelection(selection, maxSelection)) {
        event.preventDefault();
      }
    });
  }

  protected centerSelection(): void {
    this.cropperImage!.$center("contain");
    this.cropperSelection!.$center();
    this.cropperSelection!.scrollIntoView({ block: "center", inline: "center" });
  }
}

class ExactImageCropper extends ImageCropper {
  #size?: { width: number; height: number };

  public async showDialog(): Promise<File> {
    // The image already has the correct size, cropping is not necessary
    if (
      this.width == this.#size!.width &&
      this.height == this.#size!.height &&
      this.image instanceof HTMLCanvasElement
    ) {
      return this.resizer.saveFile(
        { exif: this.orientation ? undefined : this.exif, image: this.image },
        this.file.name,
        this.file.type,
      );
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
      return size.width <= this.width && size.height <= this.height;
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

    this.#size = sizes[sizes.length - 1];
    this.image = await this.resizer.resize(
      this.image as HTMLImageElement,
      this.width >= this.height ? this.width : this.#size.width,
      this.height > this.width ? this.height : this.#size.height,
      this.resizer.quality,
      true,
      timeout,
    );
  }

  protected getCropperTemplate(): string {
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

  protected setCropperStyle() {
    super.setCropperStyle();

    this.cropperSelection!.width = this.#size!.width;
    this.cropperSelection!.height = this.#size!.height;

    this.cropperCanvas!.style.width = `${this.width}px`;
    this.cropperCanvas!.style.height = `${this.height}px`;
    this.cropperSelection!.style.removeProperty("aspectRatio");
  }
}

class MinMaxImageCropper extends ImageCropper {
  #cropperCanvasRect?: DOMRect;
  constructor(element: WoltlabCoreFileUploadElement, file: File, configuration: CropperConfiguration) {
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

  protected getDialogExtra(): string {
    return getPhrase("wcf.global.button.reset");
  }

  public async loadImage(): Promise<void> {
    await super.loadImage();

    if (this.image!.width < this.minSize.width || this.image!.height < this.minSize.height) {
      throw new Error(
        getPhrase("wcf.upload.error.image.tooSmall", {
          width: this.minSize.width,
          height: this.minSize.height,
        }),
      );
    }
  }

  protected getCropperTemplate(): string {
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

  protected createCropper() {
    super.createCropper();

    this.dialog!.addEventListener("extra", () => {
      this.centerSelection();
    });

    // Limit the selection to the min/max size
    this.cropperSelection!.addEventListener("change", (event: CustomEvent) => {
      const selection = event.detail as Selection;
      this.#cropperCanvasRect = this.cropperCanvas!.getBoundingClientRect();

      const maxImageWidth = Math.min(this.image!.width, this.maxSize.width);
      const widthRatio = this.#cropperCanvasRect.width / maxImageWidth;

      const minWidth = this.minSize.width * widthRatio;
      const maxWidth = this.maxSize.width * widthRatio;
      const minHeight = minWidth / this.configuration.aspectRatio;
      const maxHeight = maxWidth / this.configuration.aspectRatio;

      if (
        selection.width < minWidth ||
        selection.height < minHeight ||
        selection.width > maxWidth ||
        selection.height > maxHeight
      ) {
        event.preventDefault();
      }
    });
  }

  protected getCanvas(): Promise<HTMLCanvasElement> {
    // Calculate the size of the image in relation to the window size
    const maxImageWidth = Math.min(this.image!.width, this.maxSize.width);
    const widthRatio = this.#cropperCanvasRect!.width / maxImageWidth;
    const width = this.cropperSelection!.width / widthRatio;
    const height = width / this.configuration.aspectRatio;

    return this.cropperSelection!.$toCanvas({
      width: Math.max(Math.min(Math.ceil(width), this.maxSize.width), this.minSize.width),
      height: Math.max(Math.min(Math.ceil(height), this.maxSize.height), this.minSize.height),
    });
  }

  protected centerSelection(): void {
    // Reset to get the maximum available height
    this.cropperCanvas!.style.height = "";

    const dimensions = DomUtil.outerDimensions(this.cropperCanvas!.parentElement!);
    this.cropperCanvas!.style.height = `${dimensions.height}px`;

    this.cropperImage!.$center("contain");
    this.#cropperCanvasRect = this.cropperImage!.getBoundingClientRect();

    if (this.configuration.aspectRatio >= 1.0) {
      this.cropperSelection!.$change(0, 0, this.#cropperCanvasRect.width, 0, this.configuration.aspectRatio, true);
    } else {
      this.cropperSelection!.$change(0, 0, 0, this.#cropperCanvasRect.height, this.configuration.aspectRatio, true);
    }
    this.cropperSelection!.$center();
    this.cropperSelection!.scrollIntoView({ block: "center", inline: "center" });
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
