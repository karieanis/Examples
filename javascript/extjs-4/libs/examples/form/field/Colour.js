/**
 * @class Examples.form.field.Colour
 * @extends Ext.form.field.Picker
 * @docauthor Jeremy Rayner 
 * @author Jeremy Rayner 
 * 
 * @todo abstract the evalution methods out of the class - maybe change to events or
 * instance methods?
 */
Ext.define('Examples.form.field.Colour', {
	extend: 'Ext.form.field.Picker',
	alias: 'widget.colourfield',
	requires: ['Ext.picker.Color'],
	uses: ['Ext.String'],
	colorRe: /^0x[a-zA-Z0-9]{6}$/,
	submitFormat: '0x{0}',
	cls: Ext.baseCSSPrefix + 'field-colour',
	trigger1Cls: Ext.baseCSSPrefix + 'form-clear-trigger',
	trigger2Cls: Ext.baseCSSPrefix + 'form-arrow-trigger',
	initValue: function() {
		var me = this,
			value = me.value;
		
		if(!Ext.isEmpty(value)) {
			if(value.match(me.colorRe)) {
				me.value = value.replace(/0x/, "");
			}
		} else {
			me.value = "";
		}
			
		me.callParent();	
	},
	createPicker: function() {
		var me = this;
			
		return Ext.create('Ext.picker.Color', {
			ownertCt: me.ownerCt,
			renderTo: document.body,
			cls: Ext.baseCSSPrefix + 'boundlist',
			floating: true,
			hidden: true,
			focusOnShow: true,
			listeners: {
				select: me.onSelect,
				scope: me
			},
			keyNavConfig: {
				esc: function() {
					me.collaspe();
				}
			}
		})
	},
	alignPicker: function() {
        var me = this,
            picker, isAbove,
            aboveSfx = '-above';

        if (this.isExpanded) {
            picker = me.getPicker();
            if (me.matchFieldWidth) {
                // Auto the height (it will be constrained by min and max width) unless there are no records to display.
                picker.setSize(me.bodyEl.getWidth(), 'auto');
            }
            if (picker.isFloating()) {
                picker.alignTo(me.inputEl, me.pickerAlign, me.pickerOffset);

                // add the {openCls}-above class if the picker was aligned above
                // the field due to hitting the bottom of the viewport
                isAbove = picker.el.getY() < me.inputEl.getY();
                me.bodyEl[isAbove ? 'addCls' : 'removeCls'](me.openCls + aboveSfx);
                picker.el[isAbove ? 'addCls' : 'removeCls'](picker.baseCls + aboveSfx);
            }
        }
    },
	setValue: function(value) {
		var me = this;

		if(!Ext.isEmpty(value)) {
			if(value.match(me.colorRe)) {
				value = value.replace(/0x/, "");
			}
		}
		
		me.callParent([value]);
	},
	onSelect: function(picker, color, o) {
		var me = this;
		
		me.setValue(color);
		me.fireEvent('select', me, color);
		me.collapse();
	},
	onExpand: function() {
		var me = this,
			value = me.getValue();
		
		if(!Ext.isEmpty(value)) {
			me.picker.select(value, true);
		}
	},
	onTrigger1Click: function() {
		var me = this;
		me.setValue(null);
	},
	onTrigger2Click: function() {
		var me = this;
		return me.onTriggerClick.apply(me, arguments);
	},
	getSubmitValue: function() {
		var me = this,
			format = me.submitFormat,
			value = me.getValue();
		
		return value ? Ext.String.format(format, value) : null;
	}
});