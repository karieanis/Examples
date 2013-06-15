/**
 * @class Examples.controller.Auth
 * @extends Ext.app.Controller
 * @author Jeremy Rayner 
 * 
 * Example authorisation controller.
 * 
 * If the user object in the app object is not authenticated, the login view should be loaded. This modal view should
 * stay visible until authentication is confirmed.
 */
Ext.define('Examples.controller.Auth', function() {
	var LOGIN = 'logout',
		LOGOUT = 'login',
		_conn;
	
	/**
	 * Since we are not using proxies for authentication, we should centralise route generation
	 * for authentication. This function is only available within the local closure scope, and
	 * is bound to the controller within the init method.
	 * 
	 * @scope Examples.controller.Auth
	 * 
	 * @param {String} type		The type of route to generate (LOGIN | LOGOUT)
	 * @return {String} route
	 */
	function generateRoute(type) {
		var me = this,
			app = me.application,
			route;
		
		switch(type) {
			case LOGIN:
				route = app.getServerRoute() + '/login.json';
			break;
			
			case LOGOUT:
				route = app.getServerRoute() + '/logout.json';
			break;
		}
		
		return route;
	}
	
	// return the public methods and properties
	return {
		extend: 'Ext.app.Controller',
		requires: ['Examples.InfoMessage',
			'Examples.view.auth.Login', 
			'Examples.model.CurrentUser', 
			'Ext.Object', 
			'Ext.String', 
			'Ext.Array', 
			'Ext.data.Connection', 
			'Ext.data.proxy.Rest',
			'Examples.Response'],
		views: 	['auth.Login'],
		statics: {
			CONTROLLER_NAME: 'Auth'
		},
		refs: [{ref: 'authwindow', selector: 'login', autoCreate: true, xtype: 'login'},
			{ref: 'authform', selector: 'login > form'}],
		/**
		 * Init method. Used to wire up listeners to views for this controller
		 * @return void
		 */
		init: function() {
			var me = this,
				app = me.application;
			
			generateRoute = Ext.bind(generateRoute, me);
			me.setConnection(Ext.ClassManager.instantiate('Ext.data.Connection'));
				
			// add auth style events to the application object
			app.addEvents(
				/**
				 * @event authchange
				 */
				'authchange',
				/**
				 * @event userloaded
				 */
				'userloaded'
			);
			
			// listen to all logout responses not firing at the moment
			Ext.util.Observable.observe(Ext.data.Connection, {
				requestexception: {
					fn: function(conn, response, o, cfg) {
						// logout
						if(response.status === Examples.Response.RESPONSE_CODES.HTTP_UNAUTHORIZED) {
							var fp = me.getAuthform(),
								data, container, message;
								
							try {
								data = Examples.Response.parseResponse(response),
								message = data.errors || [];
		
								if(!Ext.isEmpty(message)) {
									if(null === (container = fp.down('infomessage'))) {
										container = fp.insert(0, {xtype: 'infomessage'});
									}
									
									container.addCls('examples-message-error examples-icon-error');
									container.setMessage(message[0]);
								}
							} catch(e) {
								//
							}
							
							app.setCurrentUser(null);
						}
					},
					scope: app
				}
			});
			
			// listen to the authenticate button press
			me.control({
				'login > form button[action=authenticate]': {
					click: { 
						fn: me.login,
						scope: me
					}
				},
				'viewport > sidebar > component#presence': {
					// attach a listener to component el, delegate to a#logout clicks
					render: {
						fn: function(c) {
							c.getEl().on({
								click: {
									fn: me.logout,
									scope: me,
									delegate: 'a#logout'
								}
							});
						},
						scope: me
					}
				}
			});
		},
		getConnection: function() {
			return _conn;
		},
		setConnection: function(conn) {
			var me = this;
			_conn = conn;
			
			return me;
		},
		/**
		 * Fired on the application launch. Check if there is an authorised user, and set up the views accordingly.
		 * @return void
		 */
		onLaunch: function() {
			var me = this,
				app = me.application,
				conn = me.getConnection();
			
			app.on({
				authchange: {
					fn: me.onAuthChange,
					scope: me
				}
			});
				
			me.getAuthwindow().on({
				show: {
					fn: function(win) {
						win.down('form > textfield[name=username]').focus();
					},
					scope: me
				},
				hide: {
					fn: function(win) {
						var message;
						
						if(null !== (message = win.down('infomessage'))) {
							message.destroy();
						}
					},
					scope: me
				}
			})

			conn.request({
				url: generateRoute(LOGIN),
				method: 'POST',
				headers: {
					accept: Examples.Response.RESPONSE_CONTENT_TYPE_JSON,
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				success: function(response, o) {
					var data = Examples.Response.parseResponse(response);
					
					if(true == data.success) {
						Examples.model.CurrentUser.load(data.id, {
							success: function(record, operation) {
								app.setCurrentUser(record);
							}
						});
					}
				},
				failure: function(response, o) {
					me.onAuthChange(app, false);
				},
				scope: app
			});
		},
		/**
		 * Function to be fired on the authchange event
		 * @param {Ext.app.Application} app
		 * @param {Boolean} auth
		 * @return void
		 */
		onAuthChange: function(app, auth) {
			var me = this;
			
			if(true === auth) {
				me.hideAuthWindow();
				me.getAuthform().getForm().reset();
				
			} else { 
				app.setCurrentUser(null);
				me.showAuthWindow();
			}
		},
		/**
		 * Wrapper for Examples.model.User.getAuthenticated method
		 * @return {Boolean}
		 */
		isAuthenticated: function() {
			var me = this;
			return true === me.application.isAuthenticated();
		},
		/**
		 * Wrapper for Examples.auth.view.Login.show
		 * @return {Examples.controller.Auth} this
		 */
		showAuthWindow: function() {
			var me = this;
			
			me.getAuthwindow().show();
			return me;
		},
		/**
		 * Wrapper for Examples.auth.view.Login.hide
		 * @return {Examples.controller.Auth} this
		 */
		hideAuthWindow: function() {
			var me = this;
			me.getAuthwindow().hide();
			return me;
		},
		/**
		 * Confirm the user identity with the server
		 * @param {Ext.Button} button
		 * @param {Ext.EventObject} e
		 * @param {Object} o
		 * @return void
		 */
		login: function(button, e, o) {
			var me = this, 
				app = me.application,
				window = me.getAuthwindow(),
				fp = button.up('form'),
				form = fp.getForm(),
				vals = form.getValues();
				
			form.submit({
				url: generateRoute(LOGIN),
				method: 'POST',
				headers: {
					accept: Examples.Response.RESPONSE_CONTENT_TYPE_JSON,
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				success: function(form, action) {
					var response = action.response,
						type, data;

					try {
						data = Examples.Response.parseResponse(response);
						
						if(true == data.success) {
							Examples.model.CurrentUser.load(data.id, {
								success: function(record, operation) {
									app.setCurrentUser(record);
								}
							});
						} else {
							app.setCurrentUser(null);
						}
					} catch(e) {
						
					}
				},
				failure: function(form, action) {
					var response = action.response,
						tpl = Examples.view.auth.Login.getErrorTpl(),
						message, type, data, container;
						
					try {
						data = EM.Response.parseResponse(response),
						message = data.errors;

						if(null === (container = fp.down('infomessage'))) {
							container = fp.insert(0, {
								xtype: 'infomessage'
							});
						}
						
						container.addCls('examples-message-error examples-icon-error');
						container.setMessage(message[0]);
						fp.doLayout();
					} catch(e) {
						
					}
					
					app.setCurrentUser(null);
				}
			});
		},
		/**
		 * Send a logout request to the server, then toggle the authentication setting for
		 * the app object.
		 * @param {Ext.Button} button
		 * @param {Ext.EventObject} e
		 * @param {Object} o
		 * @return void
		 */
		logout: function(e, el, o) {
			var me = this,
				conn = me.getConnection(),
				app = me.application;

			Ext.MessageBox.show({
				title: 'Logout?',
				msg: 'Are you sure you want to logout?',
				icon: Ext.MessageBox.QUESTION,
				buttons: Ext.MessageBox.YESNO,
				animateTarget: e.getTarget(),
				fn: function(id, text, o) {
					if('yes' === id) {
						conn.request({
							url: generateRoute(LOGOUT),
							method: 'GET',
							headers: {
								accept: Examples.Response.RESPONSE_CONTENT_TYPE_JSON,
								'Content-Type': 'application/x-www-form-urlencoded'
							},
							callback: function(o, success, response) {
								app.setCurrentUser(null);
							},
							scope: me
						});
					}
				},
				scope: me
			});
		}
	};
}());