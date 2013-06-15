Ext.namespace('Examples.Components');

/**
 * @class Examples.Components.LockField
 * @extends Examples.Components.ToggleField
 * @author Jeremy Rayner
 * 
 * Create a new lock field.
 * 
 * @constructor
 * Create a new LockField
 * @param {Object} config Configuration options
 * @xtype lockfield
 */
Examples.Components.LockField = Ext.extend(Examples.Components.ToggleField, function() {
	// private
	var LOCKED = 1,
		UNLOCKED = 0,
		LOCKED_TIP = "This item is locked",
		UNLOCKED_TIP = "This item is not locked";
		
	// private
	function getTooltip(value) {
		if(value == UNLOCKED) {
			return UNLOCKED_TIP;
		} else if(value == LOCKED) {
			return LOCKED_TIP;
		}
	}
	
	// public methods
	return {	
		value: UNLOCKED,
		field1Class: 'x-icon-locked',
		field2Class: 'x-icon-unlocked',
		// private
		initComponent: function() {
			Examples.Components.LockField.superclass.initComponent.call(this);
			this.addEvents(
				/**
				 * @event locked
				 * Fires when this field is toggle to locked
				 * @param {Examples.Components.LockField} field
				 */
				'locked',
				/**
				 * @event unlocked
				 * Fired when this field is toggled to unlocked
				 * @param {Examples.Components.LockField} field
				 */
				'unlocked'
			);
		},
		onRender: function(ct, position) {
			Examples.Components.LockField.superclass.onRender.call(this, ct, position);
			
			this.icon.addClass('x-form-lockfield');
			this.icon.dom.qtip = getTooltip(this.value);
			this.on('toggle', this.toggleLock, this);
		},
		/**
		 * @param {Ext.Element} toggleEl The icon element
		 * @param {String} oldCls The css class which is being toggled from
		 */
		toggleLock: function(toggleEl, oldCls) {
			var newValue, 
				event;
			
			if(oldCls == this.field1Class) {
				newValue = UNLOCKED,
				event = 'unlocked';
			}
			else if(oldCls == this.field2Class) {
				newValue = LOCKED,
				event = 'locked';
			}
			
			this.setValue(newValue);
			this.fireEvent(event, this);
			
			toggleEl.replaceClass(oldCls, this.getIconCls(newValue));
			toggleEl.dom.qtip = getTooltip(newValue);
		},
		/**
		 * Get the css class for the passed value
		 * @param {Number} value
		 * @return {String} cls The css class associated with the value
		 */
		getIconCls: function(value) {
			if(value == UNLOCKED) {
				return this.field2Class;
			} else if(value == LOCKED) {
				return this.field1Class;
			}
		},
		beforeDestroy: function() {
			this.removeListener('toggle', this.toggleLock, this);
			Examples.Components.LockField.superclass.beforeDestroy.apply(this, arguments);
		}
	};
}());

Ext.reg('lockfield', Examples.Components.LockField);