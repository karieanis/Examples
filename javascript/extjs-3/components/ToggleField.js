Ext.namespace('Examples.Components');

/**
 * @class Examples.Components.ToggleField
 * @extends Ext.form.Field
 * @author Jeremy Rayner
 * 
 * ToggleField class definition. Specific types of togglable fields should extend from this
 * class.
 * 
 * @constructor
 * Create a new toggle field
 * @param {Object} config Configuration options
 * @xtype togglefield
 */
Examples.Components.ToggleField = Ext.extend(Ext.form.Field, {
	// private
	defaultAutoCreate: {tag: 'input', type: 'hidden', autocomplete: 'off'},
	/**
	 * @cfg {String} field1Class First css class which is used when toggling
	 */
	/**
	 * @cfg {String} field2Class Second css class which is used when toggling
	 */
	// private
	initComponent: function() {
		Examples.Components.ToggleField.superclass.initComponent.call(this);
		this.addEvents(
			/**
			 * @event beforetoggle
			 * Fires before toggling occurs. Return false to prevent toggling.
			 * @param {Examples.Components.ToggleField} field This togglefield
			 * @param {String} oldCls The css class being toggled from
			 * @param {String} newCls The css class which is being toggled to
			 */
			'beforetoggle',
			/**
			 * @event toggle
			 * Fires when it is okay to toggle. Child classes should handle changes in the UI as
			 * required.
			 * @param {Ext.Element} icon The icon element
			 * @param {String} oldCls The css class being toggled from
			 */
			'toggle'
		);
	},
	// private
	onRender: function(ct, position) {
		Examples.Components.ToggleField.superclass.onRender.call(this, ct, position);
			
		this.wrap = this.el.wrap({tag: 'span', cls: 'x-form-field-wrap'});
		var icon = Ext.DomHelper.append(this.wrap, {
			tag: 'img', 
			src: Ext.BLANK_IMAGE_URL, 
			cls: this.getIconCls(this.value)
		});
			
		this.icon = Ext.get(icon);
		this.icon.on('click', this.toggle, this);
	},
	/**
	 * Determine which class is the old class, and which class is the new class. After determining this,
	 * fire the appropriate events to allow child classes to handle UI changes.
	 * @param {Ext.EventObject} e
	 * @param {Html} target
	 * @param {Object} o
	 */
	toggle: function(e, target, o) {
		var icon = this.icon, 
			oldCls, newCls;
			
		if(icon.hasClass(this.field1Class)) {
			oldCls = this.field1Class;
			newCls = this.field2Class;
		} else if(icon.hasClass(this.field2Class)) {
			oldCls = this.field2Class;
			newCls = this.field1Class;
		}
		
		if(this.fireEvent('beforetoggle', this, oldCls, newCls) !== false) {
			this.fireEvent('toggle', this.icon, oldCls);
		}
	},
	// private
	beforeDestroy: function() {
		Examples.Components.ToggleField.superclass.beforeDestroy.apply(this, arguments);
		Ext.destroy(this.wrap, this.icon);
	},
	/**
	 * Abstract function to be implemented in child classes
	 */
	getIconCls: Ext.emptyFn
});

Ext.reg('togglefield', Examples.Components.ToggleField);