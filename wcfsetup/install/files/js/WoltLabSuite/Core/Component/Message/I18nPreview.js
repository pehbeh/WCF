/**
 * Provides previews for mulitple CKEditor 5 message fields.
 *
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Helper/PromiseMutex", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Component/Ckeditor/Event", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Dom/Util"], function (require, exports, tslib_1, PromiseMutex_1, Ajax_1, Event_1, Dialog_1, Language_1, Util_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    Util_1 = tslib_1.__importDefault(Util_1);
    async function loadPreview(message, objectType, objectId) {
        const response = (await (0, Ajax_1.dboAction)("getMessagePreview", "wcf\\data\\bbcode\\MessagePreviewAction")
            .payload({
            data: {
                message,
            },
            messageObjectType: objectType,
            messageObjectID: objectId,
        })
            .dispatch());
        const dialog = (0, Dialog_1.dialogFactory)()
            .fromHtml('<div class="htmlContent">' + response.message + "</div>")
            .withoutControls();
        dialog.show((0, Language_1.getPhrase)("wcf.global.preview"));
    }
    function getEditorMap(messageFieldIds) {
        const map = new Map();
        messageFieldIds.forEach((messageFieldId) => {
            (0, Event_1.listenToCkeditor)(document.getElementById(messageFieldId)).ready(({ ckeditor }) => {
                map.set(messageFieldId, ckeditor);
            });
        });
        return map;
    }
    function getActiveEditor(map) {
        let activeEditor = undefined;
        map.forEach((editor) => {
            if (editor.isVisible()) {
                activeEditor = editor;
            }
        });
        return activeEditor;
    }
    function setup(messageFieldIds, previewButtonId, objectType, objectId) {
        const map = getEditorMap(messageFieldIds);
        document.getElementById(previewButtonId)?.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => {
            const activeEditor = getActiveEditor(map);
            if (activeEditor === undefined) {
                return Promise.resolve();
            }
            if (activeEditor.getHtml() === "") {
                Util_1.default.innerError(activeEditor.element, (0, Language_1.getPhrase)("wcf.global.form.error.empty"));
                return Promise.resolve();
            }
            else {
                Util_1.default.innerError(activeEditor.element, false);
            }
            return loadPreview(activeEditor.getHtml(), objectType, objectId);
        }));
    }
});
