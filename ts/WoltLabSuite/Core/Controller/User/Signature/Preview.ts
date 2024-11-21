import { promiseMutex } from "WoltLabSuite/Core/Helper/PromiseMutex";
import { dboAction } from "WoltLabSuite/Core/Ajax";
import { listenToCkeditor } from "WoltLabSuite/Core/Component/Ckeditor/Event";

type ResponseGetMessagePreview = {
  message: string;
  raw: string;
};

let previewContainer: HTMLElement;

async function loadPreview(message: string): Promise<void> {
  const response = (await dboAction("getMessagePreview", "wcf\\data\\user\\UserProfileAction")
    .payload({
      data: {
        message,
      },
    })
    .dispatch()) as ResponseGetMessagePreview;

  if (previewContainer === undefined) {
    const template = document.getElementById("previewTemplate") as HTMLTemplateElement;
    const fragment = template.content.cloneNode(true);
    template.replaceWith(fragment);

    previewContainer = document.getElementById("previewContainer")!;
  }

  previewContainer.innerHTML = response.message;
}

export function setup(): void {
  listenToCkeditor(document.getElementById("text")!).ready(({ ckeditor }) => {
    document.getElementById("previewButton")?.addEventListener(
      "click",
      promiseMutex(() => loadPreview(ckeditor.getHtml())),
    );
  });
}
