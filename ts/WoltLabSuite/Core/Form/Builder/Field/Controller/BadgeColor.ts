/**
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 * @woltlabExcludeBundle all
 */

export class BadgeColorPreview {
  readonly #container: HTMLElement | null;
  #activeReferenceField: HTMLInputElement | null;
  readonly #defaultLabelText: string;

  constructor(fieldId: string, referenceFieldIds: string[], defaultLabelText: string) {
    this.#defaultLabelText = defaultLabelText;
    this.#container = document.getElementById(fieldId);

    if (this.#container === null) {
      throw new Error("Unknown field with id '" + fieldId + "'.");
    }

    const observer = new IntersectionObserver((entries) => {
      const entry = entries.filter((entry) => entry.isIntersecting).pop();

      if (entry) {
        this.#activeReferenceField = entry.target as HTMLInputElement;
        this.#updatePreview();
      }
    });

    referenceFieldIds.forEach((referenceFieldId: string) => {
      const referenceField = document.getElementById(referenceFieldId) as HTMLInputElement | null;
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

  #updatePreview(): void {
    const value = this.#activeReferenceField?.value.trim() || this.#defaultLabelText;
    this.#container!.querySelectorAll(".labelSelection__span.badge").forEach((span: HTMLSpanElement) => {
      span.textContent = value;
    });
  }
}
