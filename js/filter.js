'use strict';

LinkShortenerApp.filter('placekitten', function() {
  return function(input) {
    if(input == '') {
      return "http://placekitten.com/g/320/240";
    }
    return input;
  };
});
