/**
 * Deletes the current user cover photo.
 *
 * @author  Alexander Ebert
 * @copyright  2001-2019 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @woltlabExcludeBundle all
 */

import * as Ajax from "../../../Ajax";
import { AjaxCallbackObject, AjaxCallbackSetup, ResponseData } from "../../../Ajax/Data";
import DomUtil from "../../../Dom/Util";
import * as EventHandler from "../../../Event/Handler";
import * as Language from "../../../Language";
import * as UiConfirmation from "../../Confirmation";
import * as UiNotification from "../../Notification";

interface AjaxResponse extends ResponseData {
  returnValues: {
    url: string;
  };
}

class UiUserCoverPhotoDelete implements AjaxCallbackObject {
  private readonly button: HTMLAnchorElement;
  private readonly userId: number;

  /**
   * Initializes the delete handler and enables the delete button on upload.
   */
  constructor(userId: number) {
    const button = document.querySelector<HTMLAnchorElement>(".jsButtonDeleteCoverPhoto");
    if (button === null) {
      return;
    }

    this.button = button;
    this.button.addEventListener("click", (ev) => this._click(ev));
    this.userId = userId;

    EventHandler.add("com.woltlab.wcf.user", "coverPhoto", (data) => {
      if (typeof data.url === "string" && data.url.length > 0) {
        DomUtil.show(this.button.parentElement!);
      }
    });
  }

  /**
   * Handles clicks on the delete button.
   */
  _click(event: MouseEvent): void {
    event.preventDefault();

    UiConfirmation.show({
      confirm: () => Ajax.api(this),
      message: Language.get("wcf.user.coverPhoto.delete.confirmMessage"),
    });
  }

  _ajaxSuccess(data: AjaxResponse): void {
    const photo = document.querySelector<HTMLImageElement>(".userProfileHeader__coverPhotoImage")!;
    photo.src = data.returnValues.url;

    DomUtil.hide(this.button.parentElement!);

    UiNotification.show();
  }

  _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
    return {
      data: {
        actionName: "deleteCoverPhoto",
        className: "wcf\\data\\user\\UserProfileAction",
        parameters: {
          userID: this.userId,
        },
      },
    };
  }
}

let uiUserCoverPhotoDelete: UiUserCoverPhotoDelete | undefined;

/**
 * Initializes the delete handler and enables the delete button on upload.
 */
export function init(userId: number): void {
  if (!uiUserCoverPhotoDelete) {
    uiUserCoverPhotoDelete = new UiUserCoverPhotoDelete(userId);
  }
}
