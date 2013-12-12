'use strict';

function LinkShortenerCtrl($scope, $http) {
  $scope.linkURL = '';
  $scope.linkImage = "";
  $scope.linkDesc = "";
  $scope.linkTitle = "";
  $scope.shortlinks = [{
    'id': 'default',
    'enabled': true,
    'service': 'Original URL',
    'type': null,
    'api': null,
    'success': true,
    'shorturl': null,
    'showdescription': false,
    'showtitle': false,
    'showimage': false,
    'auth': 'none',
    'serviceicon': null
  }, {
    'id': 'isgd',
    'enabled': true,
    'service': 'is.gd',
    'type': 'jsonp',
    'api': 'http://is.gd/create.php?format=json&callback=JSON_CALLBACK&url=',
    'success': true,
    'shorturl': null,
    'showdescription': false,
    'showtitle': false,
    'showimage': false,
    'auth': 'none',
    'serviceicon': null
  }, {
    'id': 'facebook',
    'enabled': false,
    'service': 'Facebook',
    'type': 'fbapi',
    'api': null,
    'success': true,
    'shorturl': null,
    'showdescription': true,
    'showtitle': true,
    'showimage': true,
    'auth': 'required',
    'serviceicon': "brandico-facebook-rect"
  }];
  $http.get('/services.json').success(function(data, status) {
    for(var loop = 0; loop < data.length; loop++) {
      $scope.shortlinks.push(data[loop]);
    }
  });

  $scope.displayShortlinks = function() {
    for(var loop = 0; loop < $scope.shortlinks.length; loop++) {
      if($scope.shortlinks[loop].enabled && $scope.shortlinks[loop].shorturl) {
        return true;
      }
    }
    return false;
  };

  $scope.displayDetails = function(check) {
    for(var loop = 0; loop < $scope.shortlinks.length; loop++) {
      if($scope.shortlinks[loop].enabled && $scope.shortlinks[loop][check]) {
        return true;
      }
    }
    return false;
  };

  $scope.checkLogin = function(service) {
    if(service.type == 'liapi') {
      if(!service.authstatus) {
        $http.get(service.authlink + 'check').success(function(data, status) {
          if(data) {
            service.authstatus = data.status;
            updateViewExternal();
          }
        });
        service.authstatus = 'loggedout';
      }
      return service.authstatus;
    } else if(service.type == 'fbapi') {
      if(fbStatus == 'connected') {
        return 'loggedin';
      }
    } else if(service.type == 'get') {
      if(!service.authstatus) {
        $http.get(service.authlink + 'check').success(function(data, status) {
          if(data) {
            service.authstatus = data.status;
            updateViewExternal();
          }
        });
        service.authstatus = 'loggedout';
      }
      return service.authstatus;
    }
    return 'loggedout';
  };

  $scope.login = function(service) {
  	service.enabled = false;
    if(service.type == 'liapi') {
      window.location = service.authlink;
    } else if(service.type == 'fbapi') {
      FB.login(updateViewExternal, {
        scope: 'publish_actions,read_stream'
      });
    } else if(service.type == 'get') {
      window.location = service.authlink;
    }
  };

  $scope.logout = function(service) {
    if(service.type == 'liapi') {
      window.location = service.authlink + 'logout';
    } else if(service.type == 'fbapi') {
      FB.logout();
    } else if(service.type == 'get') {
      window.location = service.authlink + 'logout';
    }
  };

  $scope.getLink = function(service) {
    if(service.enabled) {
      if(!service.type) {
        service.shorturl = $scope.linkURL;
      } else if(service.type == 'jsonp') {
        $http.jsonp(service.api + encodeURI($scope.linkURL)).success(function(data, status) {
          if(data) {
            service.success = true;
            service.shorturl = data.shorturl;
          }
        });
      } else if(service.type == 'get') {
        $http({
          method: 'GET',
          url: service.api,
          params: {
            'url': encodeURI($scope.linkURL),
            'title': encodeURI($scope.linkTitle),
            'description': encodeURI($scope.linkDesc),
            'image': encodeURI($scope.linkImage)
          }
        }).success(function(data, status) {
          if(data) {
            service.success = data.success;
            service.shorturl = data.shorturl;
          }
        });
      } else if(service.type == 'fbapi') {
        FB.getLoginStatus(function(response) {
          if(response.status == 'connected') {
            FB.api('/me/links', 'post', {
              link: $scope.linkURL,
              name: $scope.linkTitle,
              description: $scope.linkDesc,
              picture: $scope.linkImage
            }, function(response) {
              console.log(response);
              if(response && response.id) {
                FB.api('/' + response.id, function(post) {
                  console.log(post);
                  if(post && post.link) {
                    service.success = true;
                    var sendURL = "https://www.facebook.com/"+ post.from.id +"/posts/" + post.id;
                    var bitlyapi = null;
					        	$.each($scope.shortlinks, function(){
					        		if (this.id == "bitly") bitlyapi = this.api;
					        	});
                    $http({
						          method: 'GET',
						          url: bitlyapi,
						          params: {
						            'url': encodeURI(sendURL),
						            'title': encodeURI($scope.linkTitle),
						            'description': encodeURI($scope.linkDesc),
						            'image': encodeURI($scope.linkImage)
						          }
						        }).success(function(data, status) {
						          if(data) {
						            service.success = data.success;
						            service.shorturl = data.shorturl;
						          }
						        });
                    
                    updateViewExternal();
                  }
                });
              }
            });
          }
        });
      } else if(service.type == 'liapi') {
        $http({
          method: 'GET',
          url: service.api,
          params: {
            'url': encodeURI($scope.linkURL),
            'title': encodeURI($scope.linkTitle),
            'description': encodeURI($scope.linkDesc),
            'image': encodeURI($scope.linkImage)
          }
        }).success(function(data, status) {
        	var bitlyapi = null;
        	$.each($scope.shortlinks, function(){
        		if (this.id == "bitly") bitlyapi = this.api;
        	});
          if(data) {            
            $http({
		          method: 'GET',
		          url: bitlyapi,
		          params: {
		            'url': encodeURI(data.shorturl),
		            'title': encodeURI($scope.linkTitle),
		            'description': encodeURI($scope.linkDesc),
		            'image': encodeURI($scope.linkImage)
		          }
		        }).success(function(data, status) {
		          if(data) {
		            service.success = data.success;
		            service.shorturl = data.shorturl;
		          }
		        });
          }
        });
      }
    } else {
      service.shorturl = null;
    }
  };

  $scope.submit = function() {
    for(var loop = 0; loop < $scope.shortlinks.length; loop++) {
    	if ($("input[name=" + $scope.shortlinks[loop].id + "_enabled]").prop("checked")) {
    		$scope.shortlinks[loop].enabled = true;
    	}
      $scope.getLink($scope.shortlinks[loop]);
    }
  };
}

LinkShortenerApp.controller('LinkShortenerCtrl', LinkShortenerCtrl);
