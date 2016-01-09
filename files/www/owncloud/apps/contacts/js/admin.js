$(document).ready(function() {
	$('#contacts-ldap-enabled').change(function() {
		var enabled=$(this).prop('checked')?'true':'false';
		$.when(
			enableBackend(
				'ldap',
				enabled
			))
		.then(function(response) {
			if(!response.error) {
				console.log('response', response.data);
			} else {
				console.warn('Error', response.message);
			}
		}).fail(function(response) {
			console.log(response.message);
		});
	});
});

function requestRoute(route, type, routeParams, params, additionalHeaders) {
	var isJSON = (typeof params === 'string');
	var contentType = isJSON
		? (type === 'PATCH' ? 'application/json-merge-patch' : 'application/json')
		: 'application/x-www-form-urlencoded';
	var processData = !isJSON;
	contentType += '; charset=UTF-8';
	var url = OC.generateUrl('apps/contacts/' + route, routeParams);
	var headers = {
		Accept : 'application/json; charset=utf-8'
	};
	if(typeof additionalHeaders === 'object') {
		headers = $.extend(headers, additionalHeaders);
	}
	var ajaxParams = {
		type: type,
		url: url,
		dataType: 'json',
		headers: headers,
		contentType: contentType,
		processData: processData,
		data: params
	};

	var defer = $.Deferred();

	$.ajax(ajaxParams)
		.done(function(response, textStatus, jqXHR) {
			console.log(jqXHR);
			defer.resolve(new JSONResponse(jqXHR));
		})
		.fail(function(jqXHR/*, textStatus, error*/) {
			console.log(jqXHR);
			var response = jqXHR.responseText ? $.parseJSON(jqXHR.responseText) : null;
			console.log('response', response);
			defer.reject(new JSONResponse(jqXHR));
		});

	return defer.promise();
}

function enableBackend(backend, enable, params) {
	return requestRoute(
		'backend/{backend}/{enable}',
		'GET',
		{backend: backend, enable: enable},
		JSON.stringify(params)
	);
}

var JSONResponse = function(jqXHR) {
	this.getAllResponseHeaders = jqXHR.getAllResponseHeaders;
	this.getResponseHeader = jqXHR.getResponseHeader;
	this.statusCode = jqXHR.status;
	var response = jqXHR.responseJSON;
	this.error = false;
	console.log('jqXHR', jqXHR);
	if (!response) {
		// 204 == No content
		// 304 == Not modified
		if ([204, 304].indexOf(this.statusCode) === -1) {
			this.error = true;
		}
		this.message = jqXHR.statusText;
	} else {
		// We need to allow for both the 'old' success/error status property
		// with the body in the data property, and the newer where we rely
		// on the status code, and the entire body is used.
		if (response.status === 'error'|| this.statusCode >= 400) {
			this.error = true;
			if (!response.data || !response.data.message) {
				this.message = t('contacts', 'Server error! Please inform system administator');
			} else {
				console.log('JSONResponse', response);
				this.message = (response.data && response.data.message)
					? response.data.message
					: response;
			}
		} else {
			this.data = response.data || response;
			// Kind of a hack
			if (response.metadata) {
				this.metadata = response.metadata;
			}
		}
	}
};
