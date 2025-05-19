/**
 * Prevents multiple submits of the same form by disabling the submit button.
 *
 * @author    Marcel Werk
 * @copyright 2001-2025 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 */
define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    function setup(form) {
        form.addEventListener("submit", () => {
            form.querySelectorAll("input[type=submit]").forEach((button) => {
                button.disabled = true;
            });
        });
    }
});
