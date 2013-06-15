Ext.namespace('Examples.Components');

/**
 * @class Examples.Components.ActionColumn
 * @extends Ext.grid.ActionColumn
 * 
 * Allows for specific renderers for both the column and the items within the column.
 * 
 * @author Jeremy Rayner
 * @version 1.0
 */
Examples.Components.ActionColumn = Ext.extend(Ext.grid.ActionColumn,  {
	actionTpl: (function() {
		var t = new Ext.XTemplate(
		'<img src="{icon}" class="x-action-col-icon x-action-col-{i} {iconCls}" ',
			'<tpl if="tooltip">',
				'ext:qtip="{tooltip}" ',
			'</tpl>',
		'/>');
		
		t.compile();
		return t;
	})(),
	/**
	 * Constructor
	 * @param {Object} cfg
	 */
	constructor: function(cfg) {
		var me = this,
			renderer = cfg.renderer || false,
			items = cfg.items || (me.items = [me]),
			tpl = me.actionTpl,
			cls = 'x-action-col-cell',
            l = items.length, 
            i, item, parsed;
			
		Examples.Components.ActionColumn.superclass.constructor.apply(me, arguments);

        me.renderer = function(v, meta, rec, ri, ci, store) {
            meta.css += ' '+cls;
            v = '';
            	
            for (i = 0; i < l; i++)  {
               	var a = Array.prototype.slice.call(arguments, 0),
               		item = items[i];
                		
               	parsed = {
               		icon: item.icon || Ext.BLANK_IMAGE_URL, 
               		i: String(i), 
               		iconCls: item.iconCls || '', 
               		tooltip: item.tooltip || ''
               	};
  					
                a.splice(0, 0, tpl, item, parsed);
                	
                // if an item renderer is available, delegate rendering to it
  				v += item.renderer ? item.renderer.apply(item.scope || this, a) : tpl.apply(parsed);
            }
            	
            return v;
        };
		
        	// custom cell renderer (if available)
		if(Ext.isFunction(renderer)) {
			me.afterMethod('renderer', renderer, this);
			me.setRenderer = function(fn) {
				me.removeMethodListener('renderer', renderer);
				
				renderer = fn;
				me.afterMethod('renderer', renderer, this);
			};
		}
	},
	/**
	 * Event processing
	 * 
	 * @param {String} name
	 * @param {EventObject} e
	 * @param {Ext.grid.GridPanel} grid
	 * @param {Integer} rowIndex
	 * @param {Integer} colIndex
	 * @return {Boolean}
	 */
	processEvent: function(name, e, grid, rowIndex, colIndex) {
		var m = e.getTarget().className.match(this.actionIdRe),
            item, fn, scope, args;
            
        if (m && (item = this.items[parseInt(m[1], 10)])) {
            if(name == 'click')  {
                args = [grid, rowIndex, colIndex, item, e];
                
                // if there is an item handler and a column handler..
                if(item.handler && this.handler) {
                	// fire the item handler, and if it doesn't return false, fire the column handler
                	if(item.handler.apply(item.scope || this, args) !== false)
                		this.handler.apply(this.scope || this, args);
                } else { // fire whatever handler is available
                	(fn = item.handler || this.handler) && fn.apply(item.scope||this.scope||this, args);
                }
            } else if ((name == 'mousedown') && (item.stopSelection !== false)) {
                return false;
            }
        }
        
        return Ext.grid.ActionColumn.superclass.processEvent.apply(this, arguments);
	}
});

Ext.apply(Ext.grid.Column.types, {"example-action": Examples.Components.ActionColumn});