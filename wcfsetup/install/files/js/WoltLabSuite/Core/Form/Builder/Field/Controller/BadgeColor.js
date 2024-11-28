/**
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 * @woltlabExcludeBundle all
 */
define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.BadgeColorPreview = void 0;
    class BadgeColorPreview {
        #container;
        #referenceField;
        #defaultLabelText;
        constructor(fieldId, referenceFieldId, defaultLabelText) {
            this.#defaultLabelText = defaultLabelText;
            this.#container = document.getElementById(fieldId);
            if (this.#container === null) {
                throw new Error("Unknown field with id '" + fieldId + "'.");
            }
            this.#referenceField = document.getElementById(referenceFieldId);
            if (this.#referenceField === null) {
                throw new Error("Unknown reference element '" + referenceFieldId + "'.");
            }
            this.#referenceField.addEventListener("input", () => this.#updatePreview());
            this.#updatePreview();
        }
        #updatePreview() {
            const value = this.#referenceField.value.trim() || this.#defaultLabelText;
            this.#container.querySelectorAll(".labelSelection__span.badge").forEach((span) => {
                span.textContent = value;
            });
        }
    }
    exports.BadgeColorPreview = BadgeColorPreview;
});
