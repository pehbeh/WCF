/**
 * Highlights code in the Code bbcode.
 *
 * @author	Tim Duesterhus
 * @copyright	2001-2019 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
define(["require", "exports", "tslib", "../Clipboard", "../Prism", "../Prism/Helper", "../Component/Snackbar", "../Language"], function (require, exports, tslib_1, Clipboard, Prism_1, PrismHelper, Snackbar_1, Language_1) {
    "use strict";
    Clipboard = tslib_1.__importStar(Clipboard);
    Prism_1 = tslib_1.__importDefault(Prism_1);
    PrismHelper = tslib_1.__importStar(PrismHelper);
    async function waitForIdle() {
        return new Promise((resolve, _reject) => {
            if (window.requestIdleCallback) {
                window.requestIdleCallback(resolve, { timeout: 5000 });
            }
            else {
                setTimeout(resolve, 0);
            }
        });
    }
    class Code {
        static chunkSize = 50;
        container;
        codeContainer;
        language;
        constructor(container) {
            this.container = container;
            this.codeContainer = this.container.querySelector(".codeBoxCode > code");
            this.language = Array.from(this.codeContainer.classList)
                .find((klass) => /^language-([a-z0-9_-]+)$/.test(klass))
                ?.replace(/^language-/, "");
        }
        static processAll() {
            document.querySelectorAll(".codeBox:not([data-processed])").forEach((codeBox) => {
                codeBox.dataset.processed = "1";
                const handle = new Code(codeBox);
                if (handle.language) {
                    void handle.highlight();
                }
                handle.createCopyButton();
            });
        }
        createCopyButton() {
            const header = this.container.querySelector(".codeBoxHeader");
            if (!header) {
                return;
            }
            const button = document.createElement("button");
            button.type = "button";
            button.innerHTML = '<fa-icon size="24" name="copy"></fa-icon>';
            button.classList.add("jsTooltip");
            button.title = (0, Language_1.getPhrase)("wcf.message.bbcode.code.copy");
            const clickCallback = async () => {
                await Clipboard.copyElementTextToClipboard(this.codeContainer);
                (0, Snackbar_1.showSuccessSnackbar)((0, Language_1.getPhrase)("wcf.message.bbcode.code.copy.success"));
            };
            button.addEventListener("click", () => clickCallback());
            header.appendChild(button);
        }
        async highlight() {
            if (!this.language) {
                throw new Error("No language detected");
            }
            const PrismMeta = (await new Promise((resolve_1, reject_1) => { require(["../prism-meta"], resolve_1, reject_1); }).then(tslib_1.__importStar)).default;
            if (!PrismMeta[this.language]) {
                throw new Error(`Unknown language '${this.language}'`);
            }
            this.container.classList.add("highlighting");
            // Step 1) Load the requested grammar.
            await new Promise((resolve_2, reject_2) => { require(["prism/components/prism-" + PrismMeta[this.language].file], resolve_2, reject_2); }).then(tslib_1.__importStar);
            // Step 2) Perform the highlighting into a temporary element.
            await waitForIdle();
            const grammar = Prism_1.default.languages[this.language];
            if (!grammar) {
                throw new Error(`Invalid language '${this.language}' given.`);
            }
            const container = document.createElement("div");
            container.innerHTML = Prism_1.default.highlight(this.codeContainer.textContent, grammar, this.language);
            // Step 3) Insert the highlighted lines into the page.
            // This is performed in small chunks to prevent the UI thread from being blocked for complex
            // highlight results.
            await waitForIdle();
            const originalLines = this.codeContainer.querySelectorAll(".codeBoxLine > span");
            const highlightedLines = PrismHelper.splitIntoLines(container);
            for (let chunkStart = 0, max = originalLines.length; chunkStart < max; chunkStart += Code.chunkSize) {
                await waitForIdle();
                const chunkEnd = Math.min(chunkStart + Code.chunkSize, max);
                for (let offset = chunkStart; offset < chunkEnd; offset++) {
                    const toReplace = originalLines[offset];
                    const replacement = highlightedLines.next().value;
                    toReplace.parentNode.replaceChild(replacement, toReplace);
                }
            }
            this.container.classList.remove("highlighting");
            this.container.classList.add("highlighted");
        }
    }
    return Code;
});
