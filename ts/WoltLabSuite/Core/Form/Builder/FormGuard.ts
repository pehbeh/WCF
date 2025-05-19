/**
 * Prevents multiple submits of the same form by disabling the submit button.
 *
 * @author    Marcel Werk
 * @copyright 2001-2025 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 */

export function setup(form: HTMLFormElement) {
  form.addEventListener("submit", () => {
    form.querySelectorAll<HTMLInputElement>("input[type=submit]").forEach((button) => {
      button.disabled = true;
    });
  });
}
