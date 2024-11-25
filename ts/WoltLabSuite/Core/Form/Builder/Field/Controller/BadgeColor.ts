/**
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 */

export class BadgeColorPreview {
  readonly #container: HTMLElement | null;
  readonly #referenceField: HTMLInputElement | null;
  readonly #defaultLabelText: string;

  constructor(fieldId: string, referenceFieldId: string, defaultLabelText: string) {
    this.#defaultLabelText = defaultLabelText;
    this.#container = document.getElementById(fieldId);

    if (this.#container === null) {
      throw new Error("Unknown field with id '" + fieldId + "'.");
    }
    this.#referenceField = document.getElementById(referenceFieldId) as HTMLInputElement | null;
    if (this.#referenceField === null) {
      throw new Error("Unknown reference element '" + referenceFieldId + "'.");
    }

    this.#referenceField.addEventListener("input", () => this.#updatePreview());
    this.#updatePreview();
  }

  #updatePreview(): void {
    const value = this.#referenceField!.value.trim() || this.#defaultLabelText;
    this.#container!.querySelectorAll(".labelSelection__span.badge").forEach((span: HTMLSpanElement) => {
      span.textContent = value;
    });
  }
}
