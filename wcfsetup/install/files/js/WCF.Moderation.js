"use strict";

/**
 * Namespace for moderation related classes.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2019 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
WCF.Moderation = { };

if (COMPILER_TARGET_DEFAULT) {
	/**
	 * Moderation queue management.
	 *
	 * @param        integer                queueID
	 * @param        string                redirectURL
	 * @deprecated 6.2 No longer in use.
	 */
	WCF.Moderation.Management = Class.extend({
		/**
		 * button selector
		 * @var        string
		 */
		_buttonSelector: '',
		
		/**
		 * action class name
		 * @var        string
		 */
		_className: '',
		
		/**
		 * list of templates for confirmation message by action name
		 * @var        object
		 */
		_confirmationTemplate: {},
		
		/**
		 * dialog overlay
		 * @var        jQuery
		 */
		_dialog: null,
		
		/**
		 * language item pattern
		 * @var        string
		 */
		_languageItem: '',
		
		/**
		 * action proxy
		 * @var        WCF.Action.Proxy
		 */
		_proxy: null,
		
		/**
		 * queue id
		 * @var        integer
		 */
		_queueID: 0,
		
		/**
		 * redirect URL
		 * @var        string
		 */
		_redirectURL: '',
		
		/**
		 * Initializes the moderation report management.
		 *
		 * @param        integer                queueID
		 * @param        string                redirectURL
		 * @param        string                languageItem
		 */
		init: function (queueID, redirectURL, languageItem) {
			if (!this._buttonSelector) {
				console.debug("[WCF.Moderation.Management] Missing button selector, aborting.");
				return;
			}
			else if (!this._className) {
				console.debug("[WCF.Moderation.Management] Missing class name, aborting.");
				return;
			}
			
			this._dialog = null;
			this._queueID = queueID;
			this._redirectURL = redirectURL;
			this._languageItem = languageItem;
			
			this._proxy = new WCF.Action.Proxy({
				failure: $.proxy(this._failure, this),
				success: $.proxy(this._success, this)
			});
			
			$(this._buttonSelector).click($.proxy(this._click, this));
		},
		
		/**
		 * Handles clicks on the action buttons.
		 *
		 * @param        object                event
		 */
		_click: function (event) {
			var $actionName = $(event.currentTarget).wcfIdentify();
			var $innerTemplate = '';
			if (this._confirmationTemplate[$actionName]) {
				$innerTemplate = this._confirmationTemplate[$actionName];
			}
			
			WCF.System.Confirmation.show(WCF.Language.get(this._languageItem.replace(/{actionName}/, $actionName)), $.proxy(function (action, parameters, content) {
				if (action === 'confirm') {
					var $parameters = {
						actionName: $actionName,
						className: this._className,
						objectIDs: [this._queueID]
					};
					if (this._confirmationTemplate[$actionName]) {
						$parameters.parameters = {};
						$(content).find('input, textarea').each(function (index, element) {
							var $element = $(element);
							var $value = $element.val();
							if ($element.getTagName() === 'input' && $element.attr('type') === 'checkbox') {
								if (!$element.is(':checked')) {
									$value = null;
								}
							}
							
							if ($value !== null) {
								$parameters.parameters[$element.attr('name')] = $value;
							}
						});
					}
					
					this._proxy.setOption('data', $parameters);
					this._proxy.sendRequest();
					
					$(this._buttonSelector).disable();
				}
			}, this), {}, $innerTemplate);
		},
		
		/**
		 * Handles successful AJAX requests.
		 *
		 * @param        object                data
		 * @param        string                textStatus
		 * @param        jQuery                jqXHR
		 */
		_success: function (data, textStatus, jqXHR) {
			var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
			var self = this;
			$notification.show(function () {
				window.location = self._redirectURL;
			});
		}
	});
}
else {
	WCF.Moderation.Management = Class.extend({
		_buttonSelector: "",
		_className: "",
		_confirmationTemplate: {},
		_dialog: {},
		_languageItem: "",
		_proxy: {},
		_queueID: 0,
		_redirectURL: "",
		init: function() {},
		_click: function() {},
		_success: function() {},
	});
}

/**
 * Namespace for activation related classes.
 */
WCF.Moderation.Activation = { };

if (COMPILER_TARGET_DEFAULT) {
	/**
	 * Manages disabled content within moderation.
	 *
	 * @see        WCF.Moderation.Management
	 * @deprecated 6.2 No longer in use.
	 */
	WCF.Moderation.Activation.Management = WCF.Moderation.Management.extend({
		/**
		 * @see        WCF.Moderation.Management.init()
		 */
		init: function (queueID, redirectURL) {
			this._buttonSelector = '#enableContent, #removeContent';
			this._className = 'wcf\\data\\moderation\\queue\\ModerationQueueActivationAction';
			
			this._super(queueID, redirectURL, 'wcf.moderation.activation.{actionName}.confirmMessage');
		}
	});
}
else {
	WCF.Moderation.Activation.Management = WCF.Moderation.Management.extend({
		init: function() {},
		_buttonSelector: "",
		_className: "",
		_confirmationTemplate: {},
		_dialog: {},
		_languageItem: "",
		_proxy: {},
		_queueID: 0,
		_redirectURL: "",
		_click: function() {},
		_success: function() {},
	});
}

/**
 * Namespace for report related classes.
 */
WCF.Moderation.Report = { };

/**
 * @deprecated 6.0 Use the `data-report-content="com.example.foo"` attribute on a `<button>` instead.
 */
WCF.Moderation.Report.Content = Class.extend({
	_buttons: { },
	_buttonSelector: '',
	_objectType: '',
	
	init: function(objectType, buttonSelector) {
		this._objectType = objectType;
		this._buttonSelector = buttonSelector;
		
		this._buttons = { };
		this._initButtons();
		
		WCF.DOMNodeInsertedHandler.addCallback('WCF.Moderation.Report' + this._objectType.hashCode(), $.proxy(this._initButtons, this));
	},
	
	_initButtons: function() {
		var self = this;
		$(this._buttonSelector).each((index, button) => {
			var $button = $(button);
			var $buttonID = $button.wcfIdentify();
			
			if (!self._buttons[$buttonID]) {
				self._buttons[$buttonID] = $button;

				require(["WoltLabSuite/Core/Ui/Moderation/Report"], ({ registerLegacyButton }) => {
					registerLegacyButton(button, this._objectType);
				});
			}
		});
	}
});

if (COMPILER_TARGET_DEFAULT) {
	/**
	 * Manages reported content within moderation.
	 *
	 * @see        WCF.Moderation.Management
	 * @deprecated 6.2 No longer in use.
	 */
	WCF.Moderation.Report.Management = WCF.Moderation.Management.extend({
		/**
		 * @see        WCF.Moderation.Management.init()
		 */
		init: function (queueID, redirectURL, isMarkedAsConfirmed) {
			this._buttonSelector = '#removeContent, #removeReport, #changeJustifiedStatus';
			this._className = 'wcf\\data\\moderation\\queue\\ModerationQueueReportAction';
			
			this._super(queueID, redirectURL, 'wcf.moderation.report.{actionName}.confirmMessage');
			
			this._confirmationTemplate.removeContent = $('<div class="section"><dl><dt><label for="message">' + WCF.Language.get('wcf.moderation.report.removeContent.reason') + '</label></dt><dd><textarea name="message" id="message" cols="40" rows="3" /></dd></dl></div>');
			this._confirmationTemplate.removeReport = $('<div class="section"><dl><dt></dt><dd><label><input type="checkbox" name="markAsJustified" id="markAsJustified" value="1"> ' + WCF.Language.get('wcf.moderation.report.removeReport.markAsJustified') + '</label></dd></dl></div>');
			this._confirmationTemplate.changeJustifiedStatus = $('<div class="section"><dl><dt></dt><dd><label><input type="checkbox" name="markAsJustified" id="markAsJustified" value="1"' + (isMarkedAsConfirmed ? ' checked="checked"' : '') + '> ' + WCF.Language.get('wcf.moderation.report.changeJustifiedStatus.markAsJustified') + '</label></dd></dl></div>');
		},

		/**
		 * @see        WCF.Moderation.Management._success()
		 */
		_success: function (data, textStatus, jqXHR) {
			if (data.actionName === 'changeJustifiedStatus') {
				var notification = new WCF.System.Notification();
				notification.show(() => {
					window.location.reload();
				});
			}
			else {
				this._super(data, textStatus, jqXHR);
			}
		}
	});
}
else {
	WCF.Moderation.Report.Management = WCF.Moderation.Management.extend({
		init: function() {},
		_buttonSelector: "",
		_className: "",
		_confirmationTemplate: {},
		_dialog: {},
		_languageItem: "",
		_proxy: {},
		_queueID: 0,
		_redirectURL: "",
		_click: function() {},
		_success: function() {},
	});
}
