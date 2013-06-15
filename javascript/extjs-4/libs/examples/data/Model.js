/**
 * Example base data model class
 * @author Jeremy Rayner 
 * @class Examples.data.Model
 * @extends Ext.data.Model
 */
Ext.define('Examples.data.Model', {
	extend: 'Ext.data.Model',
	requires: ['Ext.String'],
	inheritableStatics: {
		load: function(id, config) {
			config = Ext.apply({}, config);
			
			if(Ext.isObject()) {
				Ext.applyIf(config, {filters: id});
			} else {
				Ext.applyIf(config, {id: id});
			}

            config = Ext.applyIf(config, {action: 'read'});

            var operation  = Ext.create('Ext.data.Operation', config),
                scope      = config.scope || this,
                record     = null,
                callback;

            callback = function(operation) {
                if (operation.wasSuccessful()) {
                    record = operation.getRecords()[0];
                    Ext.callback(config.success, scope, [record, operation]);
                } else {
                    Ext.callback(config.failure, scope, [record, operation]);
                }
                Ext.callback(config.callback, scope, [record, operation]);
            };

            this.proxy.read(operation, callback, this);
		}
	},
	/**
	 * Check if a value is a blank value
	 * @param {Mixed} value
	 * @return {Boolean}
	 */
	isBlank: function(value) {
		return -1 !== ["", null, undefined, []].indexOf(value)
	},
	/**
	 * Extended validation methods - will allow blank fields to be valid
	 * @return {Ext.data.Errors}
	 */
	validate: function() {
		var me = this,
			fields = me.fields,
			errors = Ext.create('Ext.data.Errors'),
			validations = me.validations,
			validators = Ext.data.validations,
			length, validation, fieldName, fieldValue,
			valid, type, field, i;
			
		if(validations) {
			length = validations.length;
			
			for(i = 0; i < length; i++) {
				validation = validations[i];
				fieldName = validation.field || validation.name;
				field = fields.get(fieldName),
				type = validation.type;
				
				valid = (validators[type](validation, (fieldValue = me.get(fieldName)), me) || (field.allowBlank && me.isBlank(fieldValue)));
				
				if (!valid) {
                    errors.add({
                        field  : fieldName,
                        message: Ext.String.format(validation.message || validators[type + 'Message'], fieldValue)
                    });
                }
			}
		}
		
		return errors;
	},
	getData: function() {
		return this.data;
	}
});