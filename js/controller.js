'use strict';

function LinkShortenerCtrl($scope, $http) {
  $scope.linkURL = '';
  $scope.linkImage = "";
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
  }, {
    'id': 'linkedin',
    'enabled': false,
    'service': 'LinkedIn',
    'type': 'liapi',
    'api': null,
    'success': true,
    'shorturl': null,
    'showdescription': true,
    'showtitle': true,
    'showimage': false,
    'auth': 'required',
    'serviceicon': "brandico-linkedin-rect"
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
      if(!IN.User) {
        return 'loggedout';
      }
      if(IN.User.isAuthorized()) {
        return 'loggedin';
      }
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
    if(service.type == 'liapi') {
      if(!IN.User) {
        return;
      }
      IN.User.authorize(updateViewExternal);
    } else if(service.type == 'fbapi') {
      FB.login(updateViewExternal, {
        scope: 'publish_actions'
      });
    } else if(service.type == 'get') {
      window.location = service.authlink;
    }
  };

  $scope.logout = function(service) {
    if(service.type == 'liapi') {
      if(!IN.User) {
        return;
      }
      IN.User.logout(updateViewExternal);
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
        $http.get(service.api + encodeURI($scope.linkURL)).success(function(data, status) {
          if(data) {
            service.success = data.success;
            service.shorturl = data.shorturl;
            if(data.needauth && data.authlink) {
              service.authlink = data.authlink;
            } else {
              service.authlink = null;
            }
          }
        });
      } else if(service.type == 'fbapi') {
        FB.getLoginStatus(function(response) {
          if(response.status == 'connected') {
            FB.api('/me/feed', 'post', {
              link: $scope.linkURL,
              name: $scope.linkTitle,
              description: $scope.linkDesc,
              picture: $scope.linkImage
            }, function(response) {
              if(response && response.id) {
                FB.api('/' + response.id, function(post) {
                  if(post && post.link) {
                    service.shorturl = post.link;
                  }
                });
              }
            });
          } else if(response.status == 'not_authorized') {
            service.authlink = 'javascript:FB.login();';
          }
        });
      } else if(service.type == 'liapi') {
        IN.API.Raw('people/~/shares?format=json').method('POST').body(JSON.stringify({
          'content': {
            'title': $scope.linkTitle,
            'description': $scope.linkDesc,
            'submitted-url': $scope.linkURL,
            'submitted-image-url': $scope.linkImage
          },
          'visibility': {
            'code': 'anyone'
          }
        })).result(function(result) {

        }).error(function(error) {

        });
      }
    } else {
      service.shorturl = null;
    }
  };

  $scope.submit = function() {
    for(var loop = 0; loop < $scope.shortlinks.length; loop++) {
      $scope.getLink($scope.shortlinks[loop]);
    }
  };
}

LinkShortenerApp.controller('LinkShortenerCtrl', LinkShortenerCtrl);
