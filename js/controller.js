'use strict';

function LinkShortenerCtrl($scope, $http) {
  $scope.linkURL = '';
  $scope.shortlinks = [{
    'id': 'default',
    'enabled': true,
    'service': 'Original URL',
    'type': null,
    'api': null,
    'shorturl': null
  }, {
    'id': 'isgd',
    'enabled': true,
    'service': 'is.gd',
    'type': 'jsonp',
    'api': 'http://is.gd/create.php?format=json&callback=JSON_CALLBACK&url=',
    'shorturl': null
  }];
  $http.get('/services.json').success(function(data, status) {
    for(var loop = 0; loop < data.length; loop++) {
      $scope.shortlinks.push(data[loop]);
    }
  });

  $scope.getLink = function(service) {
    if(!service.type) {
      service.shorturl = $scope.linkURL;
    } else if(service.type == 'jsonp') {
      $http.jsonp(service.api + encodeURI($scope.linkURL)).success(function(data, status) {
        if(data) {
          service.shorturl = data.shorturl;
        }
      });
    } else if(service.type == 'get') {
      $http.get(service.api + encodeURI($scope.linkURL)).success(function(data, status) {
        if(data) {
          service.shorturl = data.shorturl;
        }
      });
    }
  };
  
  $scope.submit = function() {
    for(var loop = 0; loop < $scope.shortlinks.length; loop++) {
      $scope.getLink($scope.shortlinks[loop]);
    }
  };
}

LinkShortenerApp.controller('LinkShortenerCtrl', LinkShortenerCtrl);
