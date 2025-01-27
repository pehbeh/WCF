/**
 * Provides the touch-friendly main menu.
 *
 * @author Alexander Ebert
 * @copyright 2001-2022 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
define(["require", "exports", "tslib", "./Container", "../../../Language", "../../../Dom/Util", "../../Dropdown/Simple"], function (require, exports, tslib_1, Container_1, Language, Util_1, DropDownSimple) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.PageMenuMain = void 0;
    Container_1 = tslib_1.__importDefault(Container_1);
    Language = tslib_1.__importStar(Language);
    Util_1 = tslib_1.__importDefault(Util_1);
    DropDownSimple = tslib_1.__importStar(DropDownSimple);
    class PageMenuMain {
        container;
        mainMenu;
        mainMenuButton;
        menuItemBadges = new Map();
        menuItemProvider;
        observer;
        constructor(menuItemProvider) {
            this.mainMenu = document.querySelector(".mainMenu");
            this.menuItemProvider = menuItemProvider;
            this.mainMenuButton = document.querySelector(".pageHeaderMenuMobile");
            this.mainMenuButton.addEventListener("click", (event) => {
                event.stopPropagation();
                this.container.toggle();
            });
            this.container = new Container_1.default(this);
            this.observer = new MutationObserver((mutations) => {
                let refreshUnreadIndicator = false;
                mutations.forEach((mutation) => {
                    if (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0 || mutation.type === "characterData") {
                        refreshUnreadIndicator = true;
                    }
                });
                if (refreshUnreadIndicator) {
                    this.refreshUnreadIndicator();
                }
            });
            this.watchForChanges();
        }
        enable() {
            this.mainMenuButton.setAttribute("aria-expanded", "false");
            this.mainMenuButton.querySelector("fa-icon").setIcon("bars");
            this.refreshUnreadIndicator();
        }
        disable() {
            this.container.close();
            this.mainMenuButton.setAttribute("aria-expanded", "false");
            this.mainMenuButton.querySelector("fa-icon").setIcon("bars");
        }
        getContent() {
            const container = document.createElement("div");
            container.classList.add("pageMenuMainContainer");
            container.addEventListener("scroll", () => this.updateOverflowIndicator(container), { passive: true });
            container.append(this.buildMainMenu());
            const languageMenu = this.buildLanguageMenu();
            if (languageMenu) {
                container.append(languageMenu);
            }
            const footerMenu = this.buildFooterMenu();
            if (footerMenu) {
                container.append(footerMenu);
            }
            // Detect changes to the height of the children, for example, when a submenu is being expanded.
            const observer = new ResizeObserver(() => this.updateOverflowIndicator(container));
            Array.from(container.children).forEach((menu) => {
                observer.observe(menu);
            });
            const fragment = document.createDocumentFragment();
            fragment.append(container);
            return fragment;
        }
        getMenuButton() {
            return this.mainMenuButton;
        }
        sleep() {
            this.watchForChanges();
        }
        wakeup() {
            this.observer.disconnect();
            this.refreshUnreadIndicator();
        }
        watchForChanges() {
            this.observer.observe(this.mainMenu, {
                childList: true,
                subtree: true,
            });
        }
        buildMainMenu() {
            const boxMenu = this.mainMenu.querySelector(".boxMenu");
            const nav = this.buildMenu(boxMenu);
            nav.setAttribute("aria-label", window.PAGE_TITLE);
            nav.setAttribute("role", "navigation");
            this.showActiveMenuItem(nav);
            return nav;
        }
        showActiveMenuItem(menu) {
            const activeMenuItem = menu.querySelector('.pageMenuMainItemLink[aria-current="page"]');
            if (activeMenuItem) {
                let element = activeMenuItem;
                while (element && element.parentElement) {
                    element = element.parentElement.closest(".pageMenuMainItemList");
                    if (element) {
                        element.hidden = false;
                        const button = element.previousElementSibling;
                        button?.setAttribute("aria-expanded", "true");
                    }
                }
                // Expand the current item, if it contains menu items itself.
                const button = activeMenuItem.nextElementSibling;
                if (button) {
                    button.setAttribute("aria-expanded", "true");
                    const itemList = button.nextElementSibling;
                    itemList.hidden = false;
                }
            }
        }
        buildLanguageMenu() {
            const dropDownMenu = DropDownSimple.getDropdownMenu("pageLanguageContainer");
            if (dropDownMenu === undefined) {
                return null;
            }
            const children = [];
            const languageMapping = new Map();
            Array.from(dropDownMenu.children).forEach((listItem) => {
                const identifier = listItem.dataset.languageCode;
                const title = listItem.querySelector("span").textContent.trim();
                const icon = listItem.querySelector("img.iconFlag") || undefined;
                languageMapping.set(identifier, listItem.querySelector("a"));
                children.push({
                    active: false,
                    children: [],
                    counter: 0,
                    depth: 1,
                    identifier,
                    title,
                    icon,
                });
            });
            const icon = document.createElement("fa-icon");
            icon.setIcon("language");
            icon.size = 24;
            const menuItems = [
                {
                    active: false,
                    children,
                    counter: 0,
                    depth: 0,
                    identifier: "language",
                    title: Language.get("wcf.user.language"),
                    icon,
                },
            ];
            const nav = document.createElement("nav");
            nav.classList.add("pageMenuMainNavigation", "pageMenuMainNavigationLanguage");
            nav.append(this.buildMenuItemList(menuItems, true));
            // Forward clicks on the language to the actual language picker element.
            nav
                .querySelectorAll(".pageMenuMainItemList .pageMenuMainItemLabel[data-identifier]")
                .forEach((element) => {
                element.addEventListener("click", (event) => {
                    event.preventDefault();
                    const identifier = element.dataset.identifier;
                    languageMapping.get(identifier)?.click();
                });
            });
            return nav;
        }
        buildFooterMenu() {
            const box = document.querySelector('.box[data-box-identifier="com.woltlab.wcf.FooterMenu"]');
            if (box === null) {
                return null;
            }
            const boxMenu = box.querySelector(".boxMenu");
            const nav = this.buildMenu(boxMenu);
            nav.classList.add("pageMenuMainNavigationFooter");
            const label = box.querySelector("nav").getAttribute("aria-label");
            nav.setAttribute("aria-label", label);
            return nav;
        }
        buildMenu(boxMenu) {
            const menuItems = this.menuItemProvider.getMenuItems(boxMenu);
            const nav = document.createElement("nav");
            nav.classList.add("pageMenuMainNavigation");
            nav.append(this.buildMenuItemList(menuItems, false));
            return nav;
        }
        buildMenuItemList(menuItems, isLanguageSelection) {
            const list = document.createElement("ul");
            list.classList.add("pageMenuMainItemList");
            menuItems
                .filter((menuItem) => {
                // Remove links that have no target (`#`) and do not contain any children.
                if (!isLanguageSelection && !menuItem.link && menuItem.children.length === 0) {
                    return false;
                }
                return true;
            })
                .forEach((menuItem) => {
                list.append(this.buildMenuItem(menuItem, isLanguageSelection));
            });
            return list;
        }
        buildMenuItem(menuItem, isLanguageSelection) {
            const listItem = document.createElement("li");
            listItem.dataset.depth = menuItem.depth.toString();
            listItem.classList.add("pageMenuMainItem");
            if (menuItem.link) {
                const link = document.createElement("a");
                link.classList.add("pageMenuMainItemLink");
                link.href = menuItem.link;
                link.textContent = menuItem.title;
                if (menuItem.active) {
                    link.setAttribute("aria-current", "page");
                }
                if (menuItem.identifier) {
                    link.dataset.identifier = menuItem.identifier;
                }
                if (menuItem.openInNewWindow) {
                    link.target = "_blank";
                }
                if (menuItem.counter > 0) {
                    const counter = document.createElement("span");
                    counter.classList.add("pageMenuMainItemCounter", "badge", "badgeUpdate");
                    counter.setAttribute("aria-hidden", "true");
                    counter.textContent = menuItem.counter.toString();
                    if (menuItem.identifier !== null) {
                        this.menuItemBadges.set(menuItem.identifier, counter);
                    }
                    link.append(counter);
                }
                listItem.append(link);
            }
            else {
                const label = document.createElement("a");
                label.classList.add("pageMenuMainItemLabel");
                label.href = "#";
                if (menuItem.icon) {
                    label.append(menuItem.icon);
                    const span = document.createElement("span");
                    span.textContent = menuItem.title;
                    label.append(span);
                }
                else {
                    label.textContent = menuItem.title;
                }
                if (menuItem.identifier) {
                    label.dataset.identifier = menuItem.identifier;
                }
                if (!isLanguageSelection || menuItem.identifier === "language") {
                    label.addEventListener("click", (event) => {
                        event.preventDefault();
                        const button = label.nextElementSibling;
                        button.click();
                    });
                    // The button to expand the link group is used instead.
                    label.setAttribute("aria-hidden", "true");
                }
                listItem.append(label);
            }
            if (menuItem.children.length) {
                listItem.classList.add("pageMenuMainItemExpandable");
                const menuId = Util_1.default.getUniqueId();
                const button = document.createElement("button");
                button.type = "button";
                button.classList.add("pageMenuMainItemToggle");
                button.setAttribute("aria-expanded", "false");
                button.setAttribute("aria-controls", menuId);
                button.innerHTML = '<fa-icon size="24" name="angle-down"></fa-icon>';
                let ariaLabel = menuItem.title;
                if (menuItem.link) {
                    ariaLabel = Language.get("wcf.menu.page.button.toggle", { title: menuItem.title });
                }
                button.setAttribute("aria-label", ariaLabel);
                const list = this.buildMenuItemList(menuItem.children, isLanguageSelection);
                list.id = menuId;
                list.hidden = true;
                button.addEventListener("click", () => {
                    this.toggleList(button, list);
                });
                list.addEventListener("keydown", (event) => {
                    if (event.key === "Escape") {
                        event.preventDefault();
                        event.stopPropagation();
                        this.toggleList(button, list);
                    }
                });
                listItem.append(button, list);
            }
            return listItem;
        }
        toggleList(button, list) {
            if (list.hidden) {
                button.setAttribute("aria-expanded", "true");
                list.hidden = false;
            }
            else {
                button.setAttribute("aria-expanded", "false");
                list.hidden = true;
                if (document.activeElement !== button) {
                    button.focus();
                }
            }
        }
        refreshUnreadIndicator() {
            const hasUnreadItems = this.mainMenu.querySelector(".boxMenuLinkOutstandingItems") !== null;
            if (hasUnreadItems) {
                this.mainMenu.classList.add("pageMenuMobileButtonHasContent");
            }
            else {
                this.mainMenu.classList.remove("pageMenuMobileButtonHasContent");
            }
            const menuItems = this.menuItemProvider.getMenuItems(this.mainMenu);
            menuItems.forEach((menuItem) => this.refreshUnreadBadge(menuItem));
        }
        refreshUnreadBadge(menuItem) {
            if (menuItem.identifier !== null) {
                const counter = this.menuItemBadges.get(menuItem.identifier);
                if (counter) {
                    if (menuItem.counter === 0) {
                        counter.remove();
                        this.menuItemBadges.delete(menuItem.identifier);
                    }
                    else {
                        const value = parseInt(counter.textContent, 10);
                        if (value !== menuItem.counter) {
                            counter.textContent = menuItem.counter.toString();
                        }
                    }
                }
            }
            menuItem.children.forEach((child) => this.refreshUnreadBadge(child));
        }
        updateOverflowIndicator(container) {
            const hasOverflow = container.clientHeight < container.scrollHeight;
            if (hasOverflow) {
                if (container.scrollTop > 0) {
                    container.classList.add("pageMenuMainContainerOverflowTop");
                }
                else {
                    container.classList.remove("pageMenuMainContainerOverflowTop");
                }
                if (container.clientHeight + container.scrollTop < container.scrollHeight) {
                    container.classList.add("pageMenuMainContainerOverflowBottom");
                }
                else {
                    container.classList.remove("pageMenuMainContainerOverflowBottom");
                }
            }
            else {
                container.classList.remove("pageMenuMainContainerOverflowTop", "pageMenuMainContainerOverflowBottom");
            }
        }
    }
    exports.PageMenuMain = PageMenuMain;
    exports.default = PageMenuMain;
});
