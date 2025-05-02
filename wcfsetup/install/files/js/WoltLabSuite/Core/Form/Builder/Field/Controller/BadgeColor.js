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
        #activeReferenceField;
        #defaultLabelText;
        constructor(fieldId, referenceFieldIds, defaultLabelText) {
            this.#defaultLabelText = defaultLabelText;
            this.#container = document.getElementById(fieldId);
            if (this.#container === null) {
                throw new Error("Unknown field with id '" + fieldId + "'.");
            }
            const observer = new IntersectionObserver((entries) => {
                const entry = entries.filter((entry) => entry.isIntersecting).pop();
                if (entry) {
                    this.#activeReferenceField = entry.target;
                    this.#updatePreview();
                }
            });
            referenceFieldIds.forEach((referenceFieldId) => {
                const referenceField = document.getElementById(referenceFieldId);
                if (referenceField === null) {
                    throw new Error("Unknown reference element '" + referenceFieldId + "'.");
                }
                referenceField.addEventListener("input", () => {
                    this.#updatePreview();
                });
                observer.observe(referenceField);
            });
            this.#updatePreview();
        }
        #updatePreview() {
            const value = this.#activeReferenceField?.value.trim() || this.#defaultLabelText;
            this.#container.querySelectorAll(".labelSelection__span.badge").forEach((span) => {
                span.textContent = value;
            });
        }
    }
    exports.BadgeColorPreview = BadgeColorPreview;
});
