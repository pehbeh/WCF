/**
 * Container visibility handler implementation for a wysiwyg tab menu tab that, in addition to the
 * tab itself, also handles the visibility of the tab menu list item.
 *
 * @author  Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */

import Abstract from "./Abstract";
import * as DependencyManager from "../Manager";
import { getTabMenu } from "WoltLabSuite/Core/Component/Message/MessageTabMenu";

export class WysiwygTab extends Abstract {
  readonly #tabName: string;
  readonly #wysiwygId: string;

  constructor(containerId: string, tabName: string, wysiwygId: string) {
    super(containerId);

    this.#tabName = tabName;
    this.#wysiwygId = wysiwygId;
  }

  public checkContainer(): void {
    // only consider containers that have not been hidden by their own dependencies
    if (DependencyManager.isHiddenByDependencies(this._container)) {
      return;
    }

    const containerIsVisible = !this._container.hidden;
    const tabMenu = getTabMenu(this.#wysiwygId)!;
    const containerShouldBeVisible = !tabMenu.isHiddenTab(this.#tabName);

    if (containerIsVisible !== containerShouldBeVisible) {
      if (containerShouldBeVisible) {
        tabMenu?.showTab(this.#tabName);
      } else {
        tabMenu?.hideTab(this.#tabName);
      }

      // Check containers again to make sure parent containers can react to changing the visibility
      // of this container.
      DependencyManager.checkContainers();
    }
  }
}
