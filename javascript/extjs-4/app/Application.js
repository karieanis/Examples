/**
 * Example application object
 * @author Jeremy Rayner
 * @class Examples.Application
 * @extends Ext.app.Application
 */
Ext.define('Examples.Application', function() {
	 var _authenticated = false,
	 	_user, _win;
	 		
	/**
	 * Application level exception handling
	 * @private
	 * @param Exception
	  */
	 function _handleError(e) {
	 	Ext.Msg.show({
	 		title: 'An error has occured',
	 		icon: Ext.window.MessageBox.INFO,
	 		msg: e.message || e.msg,
	 		modal: true
	 	});
	 		
	 	return true;
	 }
	 
	 // public functions
	 return {
	 	extend: 'Ext.app.Application',
		autoCreateViewport: true,
		requires: ['Ext.XTemplate', 
			'Ext.Array',
			'Ext.ClassManager',
			'Ext.window.Window',
			'Ext.state.*',
			'Examples.*',
			'Examples.model.*'],
		controllers: ['Auth', 'Dashboard', 'Home', 'UserManagement', 'SiteManagement'],
		statics: {
			getSharedWindow: function() {
				if(false === Ext.isDefined(_win)) {
					_win = Ext.create('Examples.Window', {
						layout: 'fit', 
						modal: true,
						closeAction: 'hide',
						autoHeight: true,
						width: 400,
						border: false,
						defaults: {border: false}
					});
				}
				
				_win.removeAll();
				return _win;
			}
		},
		refs: [{ref: 'viewport', selector: 'viewport'},
			{ref: 'controlview', selector: 'viewport > mainview > control'}],
		/**
		 * Application launch method
		 * @return void
		 */
		launch: function() {
			var me = this,
				models = [];
				
			// add the body class
			Ext.getBody().addCls('examples-manage-body');
			Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider', {}));
			
			// app level error handling
			Ext.Error.handle = _handleError;

			if((models = Ext.ClassManager.getNamesByExpression('Examples.model.*')) && models.length > 0) {
				var route = me.getServerRoute() || "",
					prop = 'url';
					
				Ext.Array.each(models, function(cls, index, classes) {
					var base = Ext.ClassManager.get(cls),
						_proto_ = base.prototype,
						proxy = _proto_.getProxy();
					
					if("" !== route && proxy.hasOwnProperty(prop) && -1 == proxy[prop].indexOf(route)) {
						Ext.apply(proxy, {url: Ext.String.format("{0}{1}", route, proxy[prop] || "")});
					}
				});
			}		
		},
		getServerRoute: Ext.emptyFn,
		getCurrentUser: function() {
			return _user;
		},
		setCurrentUser: function(user) {
			var me = this,
				status;
				
			_user = user;

			try {
				if(Ext.isEmpty(_user)) {
			 		status = false;
			 	} else {
			 		status = _user.get(Examples.model.CurrentUser.USER_AUTHENTICATED);
			 	}
			} catch(e) {
				_handleError(e);
				status = false;
			}
			
			me.fireEvent('userloaded', me, _user);
			me.setAuthenticated(status);
			return me;
		},
		isAuthenticated: function() {
			return true === this.getAuthenticated();
		},
		/**
		 * Getter for _user
		 * @return Examples.model.User
		 */
		getAuthenticated: function() {
			return _authenticated;
		},
		/**
		 * Setter for _user
		 * @param {Examples.model.User} userModel
		 * @return Ext.Application
		 */
		setAuthenticated: function(val) {
			var me = this;
			
			if(val !== _authenticated) {
				_authenticated = val;
				me.fireEvent('authchange', me, val);
			}
			
			return me;
		},
		destroy: function() {
			delete _authenticated, _user, _win;
		}
	};
}());