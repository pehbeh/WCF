/**
 * Flexible message inline editor.
 *
 * @author  Alexander Ebert
 * @copyright  2001-2021 WoltLab GmbH
 * @license  GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Component/Ckeditor/Event", "../../Ajax", "../../Component/Ckeditor", "../../Core", "../../Dom/Change/Listener", "../../Dom/Util", "../../Event/Handler", "../../Language", "../Dropdown/Reusable", "../Notification", "../Screen", "../Scroll", "WoltLabSuite/Core/Component/Quote/Storage", "WoltLabSuite/Core/Component/Snackbar"], function (require, exports, tslib_1, Event_1, Ajax, Ckeditor_1, Core, Listener_1, Util_1, EventHandler, Language, UiDropdownReusable, UiNotification, UiScreen, UiScroll, Storage_1, Snackbar_1) {
    "use strict";
    Ajax = tslib_1.__importStar(Ajax);
    Core = tslib_1.__importStar(Core);
    Listener_1 = tslib_1.__importDefault(Listener_1);
    Util_1 = tslib_1.__importDefault(Util_1);
    EventHandler = tslib_1.__importStar(EventHandler);
    Language = tslib_1.__importStar(Language);
    UiDropdownReusable = tslib_1.__importStar(UiDropdownReusable);
    UiNotification = tslib_1.__importStar(UiNotification);
    UiScreen = tslib_1.__importStar(UiScreen);
    UiScroll = tslib_1.__importStar(UiScroll);
    class UiMessageInlineEditor {
        _activeDropdownElement;
        _activeElement;
        _dropdownMenu;
        _elements;
        _options;
        /**
         * Initializes the message inline editor.
         */
        constructor(opts) {
            this.init(opts);
        }
        /**
         * Helper initialization method for legacy inheritance support.
         */
        init(opts) {
            // Define the properties again, the constructor might not be
            // called in legacy implementations.
            this._activeDropdownElement = null;
            this._activeElement = null;
            this._dropdownMenu = null;
            this._elements = new WeakMap();
            this._options = Core.extend({
                canEditInline: false,
                className: "",
                containerId: 0,
                dropdownIdentifier: "",
                editorPrefix: "messageEditor",
                messageSelector: ".jsMessage",
                quoteManager: null,
            }, opts);
            this.rebuild();
            Listener_1.default.add(`Ui/Message/InlineEdit_${this._options.className}`, () => this.rebuild());
        }
        /**
         * Initializes each applicable message, should be called whenever new
         * messages are being displayed.
         */
        rebuild() {
            document.querySelectorAll(this._options.messageSelector).forEach((element) => {
                if (this._elements.has(element)) {
                    return;
                }
                const button = element.querySelector(".jsMessageEditButton");
                if (button !== null) {
                    const canEdit = Core.stringToBool(element.dataset.canEdit || "");
                    const canEditInline = Core.stringToBool(element.dataset.canEditInline || "");
                    if (this._options.canEditInline || canEditInline) {
                        button.addEventListener("click", (ev) => this._clickDropdown(element, ev));
                        button.classList.add("jsDropdownEnabled");
                        if (canEdit) {
                            button.addEventListener("dblclick", (ev) => this._click(element, ev));
                        }
                    }
                    else if (canEdit) {
                        button.addEventListener("click", (ev) => this._click(element, ev));
                    }
                }
                const messageBody = element.querySelector(".messageBody");
                const messageFooter = element.querySelector(".messageFooter");
                const messageFooterButtons = messageFooter.querySelector(".messageFooterButtons");
                const messageHeader = element.querySelector(".messageHeader");
                const messageText = messageBody.querySelector(".messageText");
                this._elements.set(element, {
                    button,
                    messageBody,
                    messageBodyEditor: null,
                    messageFooter,
                    messageFooterButtons,
                    messageHeader,
                    messageText,
                });
            });
        }
        /**
         * Handles clicks on the edit button or the edit dropdown item.
         */
        _click(element, event) {
            if (element === null) {
                element = this._activeDropdownElement;
            }
            if (event) {
                event.preventDefault();
            }
            if (this._activeElement === null) {
                this._activeElement = element;
                this._prepare();
                Ajax.api(this, {
                    actionName: "beginEdit",
                    parameters: {
                        containerID: this._options.containerId,
                        objectID: this._getObjectId(element),
                    },
                });
            }
            else if (this._activeElement !== element) {
                UiNotification.show("wcf.message.error.editorAlreadyInUse", undefined, "warning");
            }
        }
        /**
         * Creates and opens the dropdown on first usage.
         */
        _clickDropdown(element, event) {
            event.preventDefault();
            const button = event.currentTarget;
            if (button.classList.contains("dropdownToggle")) {
                return;
            }
            button.classList.add("dropdownToggle");
            button.parentElement.classList.add("dropdown");
            button.addEventListener("click", (event) => {
                event.preventDefault();
                event.stopPropagation();
                this._activeDropdownElement = element;
                let referenceElement = button;
                if (UiScreen.is("screen-sm-down") && button.clientWidth === 0) {
                    const message = button.closest(this._options.messageSelector);
                    const messageData = this._elements.get(message);
                    referenceElement = messageData.messageHeader.querySelector(".messageQuickOptions");
                }
                UiDropdownReusable.toggleDropdown(this._options.dropdownIdentifier, referenceElement);
            });
            // build dropdown
            if (this._dropdownMenu === null) {
                this._dropdownMenu = document.createElement("ul");
                this._dropdownMenu.className = "dropdownMenu";
                const items = this._dropdownGetItems();
                EventHandler.fire("com.woltlab.wcf.inlineEditor", `dropdownInit_${this._options.dropdownIdentifier}`, {
                    items: items,
                });
                this._dropdownBuild(items);
                UiDropdownReusable.init(this._options.dropdownIdentifier, this._dropdownMenu);
                UiDropdownReusable.registerCallback(this._options.dropdownIdentifier, (containerId, action) => this._dropdownToggle(containerId, action));
            }
            setTimeout(() => button.click(), 10);
        }
        /**
         * Creates the dropdown menu on first usage.
         */
        _dropdownBuild(items) {
            items.forEach((item) => {
                const listItem = document.createElement("li");
                listItem.dataset.item = item.item;
                if (item.item === "divider") {
                    listItem.className = "dropdownDivider";
                }
                else {
                    const label = document.createElement("span");
                    label.textContent = Language.get(item.label);
                    listItem.appendChild(label);
                    if (item.item === "editItem") {
                        listItem.addEventListener("click", (ev) => this._click(null, ev));
                    }
                    else {
                        listItem.addEventListener("click", (ev) => this._clickDropdownItem(ev));
                    }
                }
                this._dropdownMenu.appendChild(listItem);
            });
        }
        /**
         * Callback for dropdown toggle.
         */
        _dropdownToggle(containerId, action) {
            const elementData = this._elements.get(this._activeDropdownElement);
            const buttonParent = elementData.button.parentElement;
            if (action === "close") {
                buttonParent.classList.remove("dropdownOpen");
                elementData.messageFooterButtons.classList.remove("forceVisible");
                return;
            }
            buttonParent.classList.add("dropdownOpen");
            elementData.messageFooterButtons.classList.add("forceVisible");
            const visibility = new Map(Object.entries(this._dropdownOpen()));
            EventHandler.fire("com.woltlab.wcf.inlineEditor", `dropdownOpen_${this._options.dropdownIdentifier}`, {
                element: this._activeDropdownElement,
                visibility,
            });
            const dropdownMenu = this._dropdownMenu;
            let visiblePredecessor = false;
            const children = Array.from(dropdownMenu.children);
            children.forEach((listItem, index) => {
                const item = listItem.dataset.item;
                if (item === "divider") {
                    if (visiblePredecessor) {
                        Util_1.default.show(listItem);
                        visiblePredecessor = false;
                    }
                    else {
                        Util_1.default.hide(listItem);
                    }
                }
                else {
                    if (visibility.get(item) === false) {
                        Util_1.default.hide(listItem);
                        // check if previous item was a divider
                        if (index > 0 && index + 1 === children.length) {
                            const previousElementSibling = listItem.previousElementSibling;
                            if (previousElementSibling.dataset.item === "divider") {
                                Util_1.default.hide(previousElementSibling);
                            }
                        }
                    }
                    else {
                        Util_1.default.show(listItem);
                        visiblePredecessor = true;
                    }
                }
            });
        }
        /**
         * Returns the list of dropdown items for this type.
         */
        _dropdownGetItems() {
            // This should be an abstract method, but cannot be marked as such for backwards compatibility.
            return [];
        }
        /**
         * Invoked once the dropdown for this type is shown, expects a list of type name and a boolean value
         * to represent the visibility of each item. Items that do not appear in this list will be considered
         * visible.
         */
        _dropdownOpen() {
            // This should be an abstract method, but cannot be marked as such for backwards compatibility.
            return {};
        }
        /**
         * Invoked whenever the user selects an item from the dropdown menu, the selected item is passed as argument.
         */
        _dropdownSelect(_item) {
            // This should be an abstract method, but cannot be marked as such for backwards compatibility.
        }
        /**
         * Handles clicks on a dropdown item.
         */
        _clickDropdownItem(event) {
            event.preventDefault();
            const target = event.currentTarget;
            const item = target.dataset.item;
            const data = {
                cancel: false,
                element: this._activeDropdownElement,
                item,
            };
            EventHandler.fire("com.woltlab.wcf.inlineEditor", `dropdownItemClick_${this._options.dropdownIdentifier}`, data);
            if (data.cancel) {
                event.preventDefault();
            }
            else {
                this._dropdownSelect(item);
            }
        }
        /**
         * Prepares the message for editor display.
         */
        _prepare() {
            const data = this._elements.get(this._activeElement);
            const messageBodyEditor = document.createElement("div");
            messageBodyEditor.className = "messageBody editor";
            data.messageBodyEditor = messageBodyEditor;
            const icon = document.createElement("fa-icon");
            icon.size = 48;
            icon.setIcon("spinner");
            messageBodyEditor.appendChild(icon);
            data.messageBody.insertAdjacentElement("afterend", messageBodyEditor);
            Util_1.default.hide(data.messageBody);
            UiScroll.element(this._activeElement, undefined, "instant");
        }
        /**
         * Shows the message editor.
         */
        _showEditor(data) {
            const id = this._getEditorId();
            const activeElement = this._activeElement;
            const elementData = this._elements.get(activeElement);
            activeElement.classList.add("jsInvalidQuoteTarget");
            const parent = activeElement.parentElement;
            if (parent.classList.contains("anchorFixedHeader")) {
                parent.classList.add("disableAnchorFixedHeader");
            }
            const icon = elementData.messageBodyEditor.querySelector("fa-icon");
            icon.remove();
            const messageBody = elementData.messageBodyEditor;
            const editor = document.createElement("div");
            editor.className = "editorContainer";
            Util_1.default.setInnerHtml(editor, data.returnValues.template);
            messageBody.appendChild(editor);
            // bind buttons
            const formSubmit = editor.querySelector(".formSubmit");
            const buttonSave = formSubmit.querySelector('button[data-type="save"]');
            buttonSave.addEventListener("click", () => this._save());
            const buttonCancel = formSubmit.querySelector('button[data-type="cancel"]');
            buttonCancel.addEventListener("click", () => this._restoreMessage());
            EventHandler.add("com.woltlab.wcf.ckeditor5", `submitEditor_${id}`, (data) => {
                data.cancel = true;
                this._save();
            });
            // hide message header and footer
            Util_1.default.hide(elementData.messageHeader);
            Util_1.default.hide(elementData.messageFooter);
            window.setTimeout(() => {
                UiScroll.element(activeElement, undefined, "instant");
            }, 250);
        }
        /**
         * Restores the message view.
         */
        _restoreMessage() {
            const activeElement = this._activeElement;
            const elementData = this._elements.get(activeElement);
            this._destroyEditor();
            elementData.messageBodyEditor.remove();
            elementData.messageBodyEditor = null;
            Util_1.default.show(elementData.messageBody);
            Util_1.default.show(elementData.messageFooter);
            Util_1.default.show(elementData.messageHeader);
            activeElement.classList.remove("jsInvalidQuoteTarget");
            const parent = activeElement.parentElement;
            if (parent.classList.contains("anchorFixedHeader")) {
                parent.classList.remove("disableAnchorFixedHeader");
            }
            this._activeElement = null;
        }
        /**
         * Saves the editor message.
         */
        _save() {
            const id = this._getEditorId();
            const ckeditor = (0, Ckeditor_1.getCkeditorById)(id);
            const parameters = {
                containerID: this._options.containerId,
                data: {
                    message: ckeditor.getHtml(),
                },
                objectID: this._getObjectId(this._activeElement),
            };
            // add any available settings
            const settingsContainer = document.getElementById(`settings_${id}`);
            if (settingsContainer) {
                settingsContainer
                    .querySelectorAll("input, select, textarea")
                    .forEach((element) => {
                    if (element.nodeName === "INPUT" && (element.type === "checkbox" || element.type === "radio")) {
                        if (!element.checked) {
                            return;
                        }
                    }
                    const name = element.name;
                    if (Object.prototype.hasOwnProperty.call(parameters, name)) {
                        throw new Error(`Variable overshadowing, key '${name}' is already present.`);
                    }
                    parameters[name] = element.value.trim();
                });
            }
            let validateResult = this._validate(parameters);
            // Legacy validation methods returned a plain boolean.
            if (!(validateResult instanceof Promise)) {
                if (validateResult === false) {
                    validateResult = Promise.reject();
                }
                else {
                    validateResult = Promise.resolve();
                }
            }
            validateResult.then(() => {
                (0, Event_1.dispatchToCkeditor)(ckeditor.sourceElement).collectMetaData({ metaData: parameters });
                Ajax.api(this, {
                    actionName: "save",
                    parameters: parameters,
                });
                this._hideEditor();
            }, (e) => {
                const errorMessage = e.message;
                console.log(`Validation of post edit failed: ${errorMessage}`);
            });
        }
        /**
         * Validates the message and invokes listeners to perform additional validation.
         */
        _validate(parameters) {
            // remove all existing error elements
            this._activeElement.querySelectorAll(".innerError").forEach((el) => el.remove());
            const data = {
                api: this,
                parameters: parameters,
                valid: true,
                promises: [],
            };
            EventHandler.fire("com.woltlab.wcf.ckeditor5", `validate_${this._getEditorId()}`, data);
            if (data.valid) {
                data.promises.push(Promise.resolve());
            }
            else {
                data.promises.push(Promise.reject());
            }
            return Promise.all(data.promises);
        }
        /**
         * Throws an error by showing an inline error for the target element.
         */
        throwError(element, message) {
            Util_1.default.innerError(element, message);
        }
        /**
         * Shows the update message.
         */
        _showMessage(data) {
            const activeElement = this._activeElement;
            const elementData = this._elements.get(activeElement);
            // set new content
            Util_1.default.setInnerHtml(elementData.messageBody.querySelector(".messageText"), data.returnValues.message);
            // handle attachment list
            if (typeof data.returnValues.attachmentList === "string") {
                elementData.messageFooter
                    .querySelectorAll(".attachmentThumbnailList, .attachmentFileList")
                    .forEach((el) => el.remove());
                const element = document.createElement("div");
                Util_1.default.setInnerHtml(element, data.returnValues.attachmentList);
                let node;
                while (element.childNodes.length) {
                    node = element.childNodes[element.childNodes.length - 1];
                    elementData.messageFooter.insertBefore(node, elementData.messageFooter.firstChild);
                }
            }
            if (typeof data.returnValues.poll === "string") {
                const poll = elementData.messageBody.querySelector(".pollContainer");
                if (poll !== null) {
                    // The poll container is wrapped inside `.jsInlineEditorHideContent`.
                    poll.parentElement.remove();
                }
                if (data.returnValues.poll !== "") {
                    const pollContainer = document.createElement("div");
                    pollContainer.className = "jsInlineEditorHideContent";
                    Util_1.default.setInnerHtml(pollContainer, data.returnValues.poll);
                    elementData.messageBody.insertAdjacentElement("afterbegin", pollContainer);
                }
            }
            this._restoreMessage();
            this._updateHistory(this._getHash(this._getObjectId(activeElement)));
            (0, Snackbar_1.showDefaultSuccessSnackbar)();
        }
        /**
         * Hides the editor from view.
         */
        _hideEditor() {
            const elementData = this._elements.get(this._activeElement);
            const editorContainer = elementData.messageBodyEditor.querySelector(".editorContainer");
            Util_1.default.hide(editorContainer);
            const icon = document.createElement("fa-icon");
            icon.size = 48;
            icon.setIcon("spinner");
            elementData.messageBodyEditor.appendChild(icon);
        }
        /**
         * Restores the previously hidden editor.
         */
        _restoreEditor() {
            const elementData = this._elements.get(this._activeElement);
            const messageBodyEditor = elementData.messageBodyEditor;
            messageBodyEditor.querySelector('fa-icon[name="spinner"]').remove();
            const editorContainer = messageBodyEditor.querySelector(".editorContainer");
            if (editorContainer !== null) {
                Util_1.default.show(editorContainer);
            }
        }
        /**
         * Destroys the editor instance.
         */
        _destroyEditor() {
            const ckeditor = (0, Ckeditor_1.getCkeditorById)(this._getEditorId(), false);
            void ckeditor?.destroy().then(() => {
                ckeditor.discardDraft();
            });
        }
        /**
         * Returns the hash added to the url after successfully editing a message.
         */
        _getHash(objectId) {
            return `#message${objectId}`;
        }
        /**
         * Updates the history to avoid old content when going back in the browser
         * history.
         */
        _updateHistory(hash) {
            window.location.hash = hash;
        }
        /**
         * Returns the unique editor id.
         */
        _getEditorId() {
            return this._options.editorPrefix + this._getObjectId(this._activeElement).toString();
        }
        /**
         * Returns the element's `data-object-id` value.
         */
        _getObjectId(element) {
            return element.dataset.objectId || "";
        }
        _ajaxFailure(data) {
            const elementData = this._elements.get(this._activeElement);
            const cke = elementData.messageBodyEditor.querySelector(".ck.ck-editor");
            // handle errors occurring on editor load
            if (cke === null) {
                this._restoreMessage();
                return true;
            }
            this._restoreEditor();
            if (!data || data.returnValues === undefined || data.returnValues.realErrorMessage === undefined) {
                return true;
            }
            Util_1.default.innerError(cke, data.returnValues.realErrorMessage);
            return false;
        }
        _ajaxSuccess(data) {
            switch (data.actionName) {
                case "beginEdit":
                    this._showEditor(data);
                    break;
                case "save":
                    (0, Storage_1.clearQuotesForEditor)(this._getEditorId());
                    this._showMessage(data);
                    break;
            }
        }
        _ajaxSetup() {
            return {
                data: {
                    className: this._options.className,
                    interfaceName: "wcf\\data\\IMessageInlineEditorAction",
                },
                silent: true,
            };
        }
        /** @deprecated  3.0 - used only for backward compatibility with `WCF.Message.InlineEditor` */
        legacyEdit(containerId) {
            this._click(document.getElementById(containerId), null);
        }
    }
    return UiMessageInlineEditor;
});
