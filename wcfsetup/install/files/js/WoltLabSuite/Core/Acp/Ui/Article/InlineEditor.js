/**
 * Handles article trash, restore and delete.
 *
 * @author  Alexander Ebert
 * @copyright  2001-2019 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Component/Snackbar", "../../../Ajax", "../../../Component/Confirmation", "../../../Controller/Clipboard", "../../../Core", "../../../Dom/Util", "../../../Event/Handler", "../../../Language", "../../../Ui/Dialog"], function (require, exports, tslib_1, Snackbar_1, Ajax, Confirmation_1, ControllerClipboard, Core, Util_1, EventHandler, Language, Dialog_1) {
    "use strict";
    Ajax = tslib_1.__importStar(Ajax);
    ControllerClipboard = tslib_1.__importStar(ControllerClipboard);
    Core = tslib_1.__importStar(Core);
    Util_1 = tslib_1.__importDefault(Util_1);
    EventHandler = tslib_1.__importStar(EventHandler);
    Language = tslib_1.__importStar(Language);
    Dialog_1 = tslib_1.__importDefault(Dialog_1);
    const articles = new Map();
    class AcpUiArticleInlineEditor {
        options;
        /**
         * Initializes the ACP inline editor for articles.
         */
        constructor(objectId, options) {
            this.options = Core.extend({
                i18n: {
                    defaultLanguageId: 0,
                    isI18n: false,
                    languages: {},
                },
                redirectUrl: "",
            }, options);
            if (objectId) {
                this.initArticle(undefined, objectId);
            }
            else {
                document.querySelectorAll(".jsArticleRow").forEach((article) => this.initArticle(article, 0));
                EventHandler.add("com.woltlab.wcf.clipboard", "com.woltlab.wcf.article", (data) => this.clipboardAction(data));
            }
        }
        /**
         * Reacts to executed clipboard actions.
         */
        clipboardAction(actionData) {
            // only consider events if the action has been executed
            if (actionData.responseData !== null) {
                const callbackFunction = new Map([
                    ["com.woltlab.wcf.article.delete", (articleId) => this.triggerDelete(articleId)],
                    ["com.woltlab.wcf.article.publish", (articleId) => this.triggerPublish(articleId)],
                    ["com.woltlab.wcf.article.restore", (articleId) => this.triggerRestore(articleId)],
                    ["com.woltlab.wcf.article.trash", (articleId) => this.triggerTrash(articleId)],
                    ["com.woltlab.wcf.article.unpublish", (articleId) => this.triggerUnpublish(articleId)],
                ]);
                const triggerFunction = callbackFunction.get(actionData.data.actionName);
                if (triggerFunction) {
                    actionData.responseData.objectIDs.forEach((objectId) => triggerFunction(objectId));
                    (0, Snackbar_1.showDefaultSuccessSnackbar)();
                }
            }
            else if (actionData.data.actionName === "com.woltlab.wcf.article.setCategory") {
                const dialog = Dialog_1.default.openStatic("articleCategoryDialog", actionData.data.internalData.template, {
                    title: Language.get("wcf.acp.article.setCategory"),
                });
                const submitButton = dialog.content.querySelector("[data-type=submit]");
                submitButton.addEventListener("click", (ev) => this.submitSetCategory(ev, dialog.content));
            }
        }
        /**
         * Is called, if the set category dialog form is submitted.
         */
        submitSetCategory(event, content) {
            event.preventDefault();
            const innerError = content.querySelector(".innerError");
            const select = content.querySelector("select[name=categoryID]");
            const categoryId = parseInt(select.value);
            if (categoryId) {
                Ajax.api(this, {
                    actionName: "setCategory",
                    parameters: {
                        categoryID: categoryId,
                        useMarkedArticles: true,
                    },
                });
                if (innerError) {
                    innerError.remove();
                }
                Dialog_1.default.close("articleCategoryDialog");
            }
            else if (!innerError) {
                Util_1.default.innerError(select, Language.get("wcf.global.form.error.empty"));
            }
        }
        /**
         * Initializes an article row element.
         */
        initArticle(article, objectId) {
            let isArticleEdit = false;
            let title;
            if (!article && objectId > 0) {
                isArticleEdit = true;
                article = undefined;
            }
            else {
                objectId = parseInt(article.dataset.objectId);
                title = article.dataset.title;
            }
            const scope = article || document;
            if (isArticleEdit) {
                const languageId = this.options.i18n.isI18n ? this.options.i18n.defaultLanguageId : 0;
                const inputField = document.getElementById(`title${languageId}`);
                title = inputField.value;
            }
            const buttonDelete = scope.querySelector(".jsButtonDelete");
            buttonDelete.addEventListener("click", async () => {
                const result = await (0, Confirmation_1.confirmationFactory)().delete(title);
                if (result) {
                    this.invoke(objectId, "delete");
                }
            });
            const buttonRestore = scope.querySelector(".jsButtonRestore");
            buttonRestore.addEventListener("click", async () => {
                const result = await (0, Confirmation_1.confirmationFactory)().restore(title);
                if (result) {
                    this.invoke(objectId, "restore");
                }
            });
            const buttonTrash = scope.querySelector(".jsButtonTrash");
            buttonTrash.addEventListener("click", async () => {
                const { result } = await (0, Confirmation_1.confirmationFactory)().softDelete(title, false);
                if (result) {
                    this.invoke(objectId, "trash");
                }
            });
            if (isArticleEdit) {
                const buttonToggleI18n = scope.querySelector(".jsButtonToggleI18n");
                if (buttonToggleI18n !== null) {
                    buttonToggleI18n.addEventListener("click", () => void this.toggleI18n(objectId));
                }
            }
            articles.set(objectId, {
                buttons: {
                    delete: buttonDelete,
                    restore: buttonRestore,
                    trash: buttonTrash,
                },
                element: article,
                isArticleEdit: isArticleEdit,
            });
        }
        /**
         * Toggles an article between i18n and monolingual.
         */
        async toggleI18n(objectId) {
            const phraseType = this.options.i18n.isI18n ? "convertFromI18n" : "convertToI18n";
            const phrase = Language.get(`wcf.article.${phraseType}.question`);
            let languageSelection;
            if (this.options.i18n.isI18n) {
                const html = Object.entries(this.options.i18n.languages)
                    .map(([languageId, languageName]) => {
                    return `<label><input type="radio" name="i18nLanguage" value="${languageId}"> ${languageName}</label>`;
                })
                    .join("");
                languageSelection = document.createElement("dl");
                languageSelection.innerHTML = `
        <dt>${Language.get("wcf.acp.article.i18n.source")}</dt>
        <dd>${html}</dd>
      `;
            }
            const { result } = await (0, Confirmation_1.confirmationFactory)()
                .custom(phrase)
                .withFormElements((dialog) => {
                const p = document.createElement("p");
                p.innerHTML = Language.get(`wcf.article.${phraseType}.description`);
                dialog.content.append(p);
                if (languageSelection !== undefined) {
                    dialog.content.append(languageSelection);
                    dialog.incomplete = true;
                    languageSelection.querySelectorAll('input[name="i18nLanguage"]').forEach((input) => {
                        input.addEventListener("change", () => (dialog.incomplete = false), { once: true });
                    });
                }
            });
            if (result) {
                let languageId = 0;
                if (languageSelection !== undefined) {
                    const input = languageSelection.querySelector("input[name='i18nLanguage']:checked");
                    languageId = parseInt(input.value);
                }
                Ajax.api(this, {
                    actionName: "toggleI18n",
                    objectIDs: [objectId],
                    parameters: {
                        languageID: languageId,
                    },
                });
            }
        }
        /**
         * Invokes the selected action.
         */
        invoke(objectId, actionName) {
            Ajax.api(this, {
                actionName: actionName,
                objectIDs: [objectId],
            });
        }
        /**
         * Handles an article being deleted.
         */
        triggerDelete(articleId) {
            const article = articles.get(articleId);
            if (!article) {
                // The affected article might be hidden by the filter settings.
                return;
            }
            if (article.isArticleEdit) {
                window.location.href = this.options.redirectUrl;
            }
            else {
                const tbody = article.element.parentElement;
                article.element.remove();
                if (tbody.querySelector("tr") === null) {
                    window.location.reload();
                }
            }
        }
        /**
         * Handles publishing an article via clipboard.
         */
        triggerPublish(articleId) {
            const article = articles.get(articleId);
            if (!article) {
                // The affected article might be hidden by the filter settings.
                return;
            }
            if (article.isArticleEdit) {
                // unsupported
            }
            else {
                const notice = article.element.querySelector(".jsUnpublishedArticle");
                notice.remove();
            }
        }
        /**
         * Handles an article being restored.
         */
        triggerRestore(articleId) {
            const article = articles.get(articleId);
            if (!article) {
                // The affected article might be hidden by the filter settings.
                return;
            }
            Util_1.default.hide(article.buttons.delete);
            Util_1.default.hide(article.buttons.restore);
            Util_1.default.show(article.buttons.trash);
            if (article.isArticleEdit) {
                const notice = document.querySelector(".jsArticleNoticeTrash");
                notice.hidden = true;
            }
            else {
                const icon = article.element.querySelector(".jsIconDeleted");
                icon.remove();
            }
        }
        /**
         * Handles an article being trashed.
         */
        triggerTrash(articleId) {
            const article = articles.get(articleId);
            if (!article) {
                // The affected article might be hidden by the filter settings.
                return;
            }
            Util_1.default.show(article.buttons.delete);
            Util_1.default.show(article.buttons.restore);
            Util_1.default.hide(article.buttons.trash);
            if (article.isArticleEdit) {
                const notice = document.querySelector(".jsArticleNoticeTrash");
                notice.hidden = false;
            }
            else {
                const badge = document.createElement("span");
                badge.className = "badge label red jsIconDeleted";
                badge.textContent = Language.get("wcf.message.status.deleted");
                const h3 = article.element.querySelector(".containerHeadline > h3");
                h3.insertAdjacentElement("afterbegin", badge);
            }
        }
        /**
         * Handles unpublishing an article via clipboard.
         */
        triggerUnpublish(articleId) {
            const article = articles.get(articleId);
            if (!article) {
                // The affected article might be hidden by the filter settings.
                return;
            }
            if (article.isArticleEdit) {
                // unsupported
            }
            else {
                const badge = document.createElement("span");
                badge.className = "badge jsUnpublishedArticle";
                badge.textContent = Language.get("wcf.acp.article.publicationStatus.unpublished");
                const h3 = article.element.querySelector(".containerHeadline > h3");
                const a = h3.querySelector("a");
                h3.insertBefore(badge, a);
                h3.insertBefore(document.createTextNode(" "), a);
            }
        }
        _ajaxSuccess(data) {
            let notificationCallback;
            switch (data.actionName) {
                case "delete":
                    this.triggerDelete(data.objectIDs[0]);
                    break;
                case "restore":
                    this.triggerRestore(data.objectIDs[0]);
                    break;
                case "setCategory":
                case "toggleI18n":
                    notificationCallback = () => window.location.reload();
                    break;
                case "trash":
                    this.triggerTrash(data.objectIDs[0]);
                    break;
            }
            (0, Snackbar_1.showDefaultSuccessSnackbar)().addEventListener("snackbar:close", () => {
                if (notificationCallback) {
                    notificationCallback();
                }
            });
            ControllerClipboard.reload();
        }
        _ajaxSetup() {
            return {
                data: {
                    className: "wcf\\data\\article\\ArticleAction",
                },
            };
        }
    }
    return AcpUiArticleInlineEditor;
});
