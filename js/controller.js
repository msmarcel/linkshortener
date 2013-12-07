'use strict';

function LinkShortenerCtrl($scope, $http) {
	$scope.linkURL = '';
	$scope.linkImage = "http://placekitten.com/g/320/240";
	$scope.shortlinks = [
			{
				'id' : 'default',
				'enabled' : true,
				'service' : 'Original URL',
				'type' : null,
				'api' : null,
				'success' : true,
				'shorturl' : null,
				'showdescription' : false,
				'showtitle' : false,
				'showimage' : false
			},
			{
				'id' : 'isgd',
				'enabled' : true,
				'service' : 'is.gd',
				'type' : 'jsonp',
				'api' : 'http://is.gd/create.php?format=json&callback=JSON_CALLBACK&url=',
				'success' : true,
				'shorturl' : null,
				'showdescription' : false,
				'showtitle' : false,
				'showimage' : false
			}, {
				'id' : 'facebook',
				'enabled' : true,
				'service' : 'Facebook',
				'type' : 'fbapi',
				'api' : null,
				'success' : true,
				'shorturl' : null,
				'showdescription' : true,
				'showtitle' : true,
				'showimage' : true
			} ];
	$http.get('/services.json').success(function(data, status) {
		for ( var loop = 0; loop < data.length; loop++) {
			$scope.shortlinks.push(data[loop]);
		}
	});

	$scope.displayShortlinks = function() {
		for ( var loop = 0; loop < $scope.shortlinks.length; loop++) {
			if ($scope.shortlinks[loop].enabled
					&& $scope.shortlinks[loop].shorturl) {
				return true;
			}
		}
		return false;
	};

	$scope.displayDetails = function(check) {
		for ( var loop = 0; loop < $scope.shortlinks.length; loop++) {
			if ($scope.shortlinks[loop].enabled
					&& $scope.shortlinks[loop][check] ) {
				return true;
			}
		}
		return false;
	}

	$scope.getLink = function(service) {
		if (service.enabled) {
			if (!service.type) {
				service.shorturl = $scope.linkURL;
			} else if (service.type == 'jsonp') {
				$http.jsonp(service.api + encodeURI($scope.linkURL)).success(
						function(data, status) {
							if (data) {
								service.success = true;
								service.shorturl = data.shorturl;
							}
						});
			} else if (service.type == 'get') {
				$http.get(service.api + encodeURI($scope.linkURL)).success(
						function(data, status) {
							if (data) {
								service.success = data.success;
								service.shorturl = data.shorturl;
								if (data.needauth && data.authlink) {
									service.authlink = data.authlink;
								} else {
									service.authlink = null;
								}
							}
						});
			} else if (service.type == 'fbapi') {
				FB.getLoginStatus(function(response) {
					if (response.status == 'connected') {
						// FB.api();
					} else if (response.status == 'not_authorized') {
						service.authlink = 'javascript:FB.login();';
					}
				});
			}
		} else {
			service.shorturl = null;
		}
	};

	$scope.submit = function() {
		for ( var loop = 0; loop < $scope.shortlinks.length; loop++) {
			$scope.getLink($scope.shortlinks[loop]);
		}
	};
}

LinkShortenerApp.controller('LinkShortenerCtrl', LinkShortenerCtrl);
