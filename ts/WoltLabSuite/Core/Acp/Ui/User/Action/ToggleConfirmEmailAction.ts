/**
 * Handles a toggle confirm email button.
 *
 * @author  Joshua Ruesweg
 * @copyright  2001-2021 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.5
 */

import AbstractUserAction from "./Abstract";
import * as Ajax from "../../../../Ajax";
import * as Core from "../../../../Core";
import { AjaxCallbackSetup, DatabaseObjectActionResponse } from "../../../../Ajax/Data";
import { showDefaultSuccessSnackbar } from "WoltLabSuite/Core/Component/Snackbar";

export class ToggleConfirmEmailAction extends AbstractUserAction {
  public constructor(button: HTMLElement, userId: number, userDataElement: HTMLElement) {
    super(button, userId, userDataElement);

    this.button.addEventListener("click", (event) => {
      event.preventDefault();
      const isEmailConfirmed = Core.stringToBool(this.userDataElement.dataset.emailConfirmed!);

      Ajax.api(this, {
        actionName: isEmailConfirmed ? "unconfirmEmail" : "confirmEmail",
      });
    });
  }

  _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
    return {
      data: {
        className: "wcf\\data\\user\\UserAction",
        objectIDs: [this.userId],
      },
    };
  }

  _ajaxSuccess(data: DatabaseObjectActionResponse): void {
    data.objectIDs.forEach((objectId) => {
      if (~~objectId == this.userId) {
        switch (data.actionName) {
          case "confirmEmail":
            this.userDataElement.dataset.emailConfirmed = "true";
            this.button.textContent = this.button.dataset.unconfirmEmailMessage!;
            break;

          case "unconfirmEmail":
            this.userDataElement.dataset.emailConfirmed = "false";
            this.button.textContent = this.button.dataset.confirmEmailMessage!;
            break;

          default:
            throw new Error("Unreachable");
        }
      }
    });

    showDefaultSuccessSnackbar();
  }
}

export default ToggleConfirmEmailAction;
