define(["require", "exports", "tslib", "../../../Ajax", "../../../Core", "../../../Language", "../../../Dom/Util", "../../../Ui/Dialog", "WoltLabSuite/Core/Ajax/Backend"], function (require, exports, tslib_1, Ajax, Core, Language, Util_1, Dialog_1, Backend_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.AcpUiDataImportManager = void 0;
    Ajax = tslib_1.__importStar(Ajax);
    Core = tslib_1.__importStar(Core);
    Language = tslib_1.__importStar(Language);
    Util_1 = tslib_1.__importDefault(Util_1);
    Dialog_1 = tslib_1.__importDefault(Dialog_1);
    class AcpUiDataImportManager {
        queue;
        redirectUrl;
        currentAction = "";
        index = -1;
        cacheClearEndpoint = "";
        constructor(queue, redirectUrl, cacheClearEndpoint) {
            this.queue = queue;
            this.redirectUrl = redirectUrl;
            this.cacheClearEndpoint = cacheClearEndpoint;
            void this.invoke();
        }
        async invoke() {
            this.index++;
            if (this.index >= this.queue.length) {
                await (0, Backend_1.prepareRequest)(this.cacheClearEndpoint).post().fetchAsResponse();
                this.showCompletedDialog();
            }
            else {
                this.run(Language.get("wcf.acp.dataImport.data." + this.queue[this.index]), this.queue[this.index]);
            }
        }
        run(currentAction, objectType) {
            this.currentAction = currentAction;
            Ajax.api(this, {
                parameters: {
                    objectType,
                },
            });
        }
        showCompletedDialog() {
            const content = Dialog_1.default.getDialog(this).content;
            content.querySelector("h1").textContent = Language.get("wcf.acp.dataImport.completed");
            const spinner = content.querySelector("fa-icon");
            spinner.setIcon("check");
            const formSubmit = document.createElement("div");
            formSubmit.className = "formSubmit";
            formSubmit.innerHTML = `<button type="button" class="button buttonPrimary">${Language.get("wcf.global.button.next")}</button>`;
            content.appendChild(formSubmit);
            Dialog_1.default.rebuild(this);
            const button = formSubmit.children[0];
            button.addEventListener("click", (event) => {
                event.preventDefault();
                window.location.href = this.redirectUrl;
            });
            button.focus();
        }
        updateProgress(title, progress) {
            const content = Dialog_1.default.getDialog(this).content;
            const progressElement = content.querySelector("progress");
            content.querySelector("h1").textContent = title;
            progressElement.value = progress;
            progressElement.nextElementSibling.textContent = `${progress}%`;
        }
        _ajaxSetup() {
            return {
                data: {
                    className: "wcf\\system\\worker\\ImportWorker",
                },
                silent: true,
                url: "index.php?worker-proxy/&t=" + Core.getXsrfToken(),
            };
        }
        _ajaxSuccess(data) {
            if (typeof data.template === "string") {
                Dialog_1.default.open(this, data.template);
            }
            this.updateProgress(this.currentAction, data.progress);
            if (data.progress < 100) {
                Ajax.api(this, {
                    loopCount: data.loopCount,
                    parameters: data.parameters,
                });
            }
            else {
                void this.invoke();
            }
        }
        _dialogSetup() {
            return {
                id: Util_1.default.getUniqueId(),
                options: {
                    closable: false,
                    title: Language.get("wcf.acp.dataImport"),
                },
                source: null,
            };
        }
    }
    exports.AcpUiDataImportManager = AcpUiDataImportManager;
    exports.default = AcpUiDataImportManager;
});
