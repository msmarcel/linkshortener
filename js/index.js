/**
 * 
 */

$(document).ready(function() {
  $.ajaxSetup({
    cache: true
  });
  $.ajax({
    url: '//connect.facebook.net/en_US/all.js',
    dataType: 'script',
    cache: true
  }).done(function() {
    FB.init({
      appId: '181808688690091'
    });
  });
});
