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
      IN.User.authorize();
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
                    service.shorturl = post.link;
                    updateViewExternal();
                  }
                });
              }
            });
          }
        });
      } else if(service.type == 'liapi') {
        var post = {
          'content': {
            'submitted-url': $scope.linkURL,
          },
          'visibility': {
            'code': 'anyone'
          }
        };
        if($scope.linkTitle) {
          post['content']['title'] = $scope.linkTitle;
        }
        if($scope.linkDesc) {
          post['content']['description'] = $scope.linkDesc;
        }
        if($scope.linkImage) {
          post['content']['submitted-image-url'] = $scope.linkImage;
        }
        IN.API.Raw('people/~/shares').method('POST').body(JSON.stringify(post)).result(function(result) {
          console.log(result);
        }).error(function(error) {
          console.log(error);
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
