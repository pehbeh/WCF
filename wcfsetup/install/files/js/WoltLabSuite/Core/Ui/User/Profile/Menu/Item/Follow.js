/**
 * @woltlabExcludeBundle all
 */
define(["require", "exports", "tslib", "../../../../../Language", "./Abstract", "WoltLabSuite/Core/Component/Snackbar"], function (require, exports, tslib_1, Language, Abstract_1, Snackbar_1) {
    "use strict";
    Language = tslib_1.__importStar(Language);
    Abstract_1 = tslib_1.__importDefault(Abstract_1);
    /**
     * @deprecated 6.2 Use `WoltLabSuite/Core/Component/User/Follow` instead.
     */
    class UiUserProfileMenuItemFollow extends Abstract_1.default {
        constructor(userId, isActive) {
            super(userId, isActive);
        }
        _getLabel() {
            return Language.get("wcf.user.button." + (this._isActive ? "un" : "") + "follow");
        }
        _getAjaxActionName() {
            return this._isActive ? "unfollow" : "follow";
        }
        _ajaxSuccess(data) {
            this._isActive = !!data.returnValues.following;
            this._updateButton();
            (0, Snackbar_1.showDefaultSuccessSnackbar)();
        }
        _ajaxSetup() {
            return {
                data: {
                    className: "wcf\\data\\user\\follow\\UserFollowAction",
                },
            };
        }
    }
    return UiUserProfileMenuItemFollow;
});
