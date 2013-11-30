'use strict';

function LinkShortenerCtrl($scope, $http) {
  $scope.linkURL = '';
  $scope.shortlinks = [];
  
  $scope.submit = function() {
    delete $scope.shortlinks;
    $scope.shortlinks = [];
    $scope.shortlinks.push({
      'id': 'default',
      'service': 'Original URL',
      'shorturl': $scope.linkURL
    });
    $http.jsonp('http://is.gd/create.php?format=json&callback=JSON_CALLBACK&url=' + encodeURI($scope.linkURL)).success(function(data, status) {
      if(data) {
        $scope.shortlinks.push({
          'id': 'isgd',
          'service': 'is.gd',
          'shorturl': data.shorturl
        });
      }
    });
  };
}

LinkShortenerApp.controller('LinkShortenerCtrl', LinkShortenerCtrl);
