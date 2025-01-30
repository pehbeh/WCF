/**
 * ACP Style related classes.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2019 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
WCF.ACP.Style = { };

/**
 * Handles style duplicating.
 * 
 * @param	integer		styleID
 */
WCF.ACP.Style.CopyStyle = Class.extend({
	/**
	 * style id
	 * @var	integer
	 */
	_styleID: 0,
	
	/**
	 * Initializes the WCF.ACP.Style.CopyStyle class.
	 * 
	 * @param	integer		styleID
	 */
	init: function(styleID) {
		this._styleID = styleID;
		
		var self = this;
		$('.jsCopyStyle').click(function() {
			WCF.System.Confirmation.show(WCF.Language.get('wcf.acp.style.copyStyle.confirmMessage'), $.proxy(self._copy, self), undefined, undefined, true);
		});
	},
	
	/**
	 * Invokes the style duplicating process.
	 * 
	 * @param	string		action
	 */
	_copy: function(action) {
		if (action === 'confirm') {
			new WCF.Action.Proxy({
				autoSend: true,
				data: {
					actionName: 'copy',
					className: 'wcf\\data\\style\\StyleAction',
					objectIDs: [ this._styleID ]
				},
				success: $.proxy(this._success, this)
			});
		}
	},
	
	/**
	 * Redirects to newly created style.
	 * 
	 * @param	object		data
	 * @param	string		textStatus
	 * @param	jQuery		jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		window.location = data.returnValues.redirectURL;
	}
});
