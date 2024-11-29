/**
 * @woltlabExcludeBundle all
 */

import * as Language from "../../../../../Language";
import { AjaxCallbackSetup, ResponseData } from "../../../../../Ajax/Data";
import * as UiNotification from "../../../../Notification";
import UiUserProfileMenuItemAbstract from "./Abstract";

interface AjaxResponse extends ResponseData {
  returnValues: {
    following: 1 | 0;
  };
}

/**
 * @deprecated 6.2 Use `WoltLabSuite/Core/Component/User/Follow` instead.
 */
class UiUserProfileMenuItemFollow extends UiUserProfileMenuItemAbstract {
  constructor(userId: number, isActive: boolean) {
    super(userId, isActive);
  }

  protected _getLabel(): string {
    return Language.get("wcf.user.button." + (this._isActive ? "un" : "") + "follow");
  }

  protected _getAjaxActionName(): string {
    return this._isActive ? "unfollow" : "follow";
  }

  _ajaxSuccess(data: AjaxResponse): void {
    this._isActive = !!data.returnValues.following;
    this._updateButton();

    UiNotification.show();
  }

  _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
    return {
      data: {
        className: "wcf\\data\\user\\follow\\UserFollowAction",
      },
    };
  }
}

export = UiUserProfileMenuItemFollow;
