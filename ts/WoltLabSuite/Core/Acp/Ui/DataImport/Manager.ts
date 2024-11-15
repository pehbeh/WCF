/**
 * Provides the program logic for the data import function.
 *
 * @author  Marcel Werk
 * @copyright  2001-2021 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle all
 */
import * as Ajax from "../../../Ajax";
import * as Core from "../../../Core";
import * as Language from "../../../Language";
import { AjaxCallbackObject, AjaxCallbackSetup } from "../../../Ajax/Data";
import { AjaxResponse } from "../../../Controller/Clipboard/Data";
import { DialogCallbackSetup } from "../../../Ui/Dialog/Data";
import DomUtil from "../../../Dom/Util";
import UiDialog from "../../../Ui/Dialog";
import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";

export class AcpUiDataImportManager implements AjaxCallbackObject {
  private readonly queue: string[];
  private readonly redirectUrl: string;
  private currentAction = "";
  private index = -1;
  private cacheClearEndpoint = "";

  constructor(queue: string[], redirectUrl: string, cacheClearEndpoint: string) {
    this.queue = queue;
    this.redirectUrl = redirectUrl;
    this.cacheClearEndpoint = cacheClearEndpoint;

    void this.invoke();
  }

  private async invoke(): Promise<void> {
    this.index++;
    if (this.index >= this.queue.length) {
      await prepareRequest(this.cacheClearEndpoint).post().fetchAsResponse();
      this.showCompletedDialog();
    } else {
      this.run(Language.get("wcf.acp.dataImport.data." + this.queue[this.index]), this.queue[this.index]);
    }
  }

  private run(currentAction: string, objectType: string): void {
    this.currentAction = currentAction;
    Ajax.api(this, {
      parameters: {
        objectType,
      },
    });
  }

  private showCompletedDialog(): void {
    const content = UiDialog.getDialog(this)!.content;
    content.querySelector("h1")!.textContent = Language.get("wcf.acp.dataImport.completed");
    const spinner = content.querySelector("fa-icon")!;
    spinner.setIcon("check");

    const formSubmit = document.createElement("div");
    formSubmit.className = "formSubmit";
    formSubmit.innerHTML = `<button type="button" class="button buttonPrimary">${Language.get(
      "wcf.global.button.next",
    )}</button>`;

    content.appendChild(formSubmit);
    UiDialog.rebuild(this);

    const button = formSubmit.children[0] as HTMLButtonElement;
    button.addEventListener("click", (event) => {
      event.preventDefault();
      window.location.href = this.redirectUrl;
    });
    button.focus();
  }

  private updateProgress(title: string, progress: number): void {
    const content = UiDialog.getDialog(this)!.content;
    const progressElement = content.querySelector("progress")!;

    content.querySelector("h1")!.textContent = title;
    progressElement.value = progress;
    progressElement.nextElementSibling!.textContent = `${progress}%`;
  }

  _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
    return {
      data: {
        className: "wcf\\system\\worker\\ImportWorker",
      },
      silent: true,
      url: "index.php?worker-proxy/&t=" + Core.getXsrfToken(),
    };
  }

  _ajaxSuccess(data: AjaxResponse): void {
    if (typeof data.template === "string") {
      UiDialog.open(this, data.template);
    }

    this.updateProgress(this.currentAction, data.progress);

    if (data.progress < 100) {
      Ajax.api(this, {
        loopCount: data.loopCount,
        parameters: data.parameters,
      });
    } else {
      void this.invoke();
    }
  }

  _dialogSetup(): ReturnType<DialogCallbackSetup> {
    return {
      id: DomUtil.getUniqueId(),
      options: {
        closable: false,
        title: Language.get("wcf.acp.dataImport"),
      },
      source: null,
    };
  }
}

export default AcpUiDataImportManager;
