<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array(
    'link_services' => array(
        'bitly' => array(
            'client_id' => 'b4ae6ba623cf7409f817389d9b619ba73dba379a',
            'client_secret' => 'c84e8ace2b37b55cd0e93763fb773c4dbcd3ff4d',
            'authorization_url' => 'https://bitly.com/oauth/authorize',
            'access_token_url' => 'https://api-ssl.bitly.com/oauth/access_token'
        ),
        'twitter' => array(
            'consumerKey' => '3XQRpIgpSm7XtyFtPC7fmQ',
            'consumerSecret' => 'QJyUf4CFVsw0dZx9tAJWSi6WPGYOTNBiKd8CHqJtIc',
            'siteUrl' => 'https://api.twitter.com/oauth'
        ),
        'google' => array(
            'client_id' => '116155492975-jiimc1pnbn8rutaif71tb9bf0udj7qn0.apps.googleusercontent.com',
            'client_secret' => '8LcgdVHWFdP--sQNFdcVqT1E',
            'scope' => 'https://www.googleapis.com/auth/urlshortener',
            'authorization_url' => 'https://accounts.google.com/o/oauth2/auth',
            'access_token_url' => 'https://accounts.google.com/o/oauth2/token'
        ),
        'linkedin' => array(
            'client_id' => '75rsw6wrww3h5k',
            'client_secret' => 'UQRyprGm4vsW06vc',
            'scope' => 'rw_nus',
            'state' => 'linkshortener',
            'authorization_url' => 'https://www.linkedin.com/uas/oauth2/authorization',
            'access_token_url' => 'https://api.linkedin.com/uas/oauth2/accessToken',
            'request_token_url' => 'https://api.linkedin.com/uas/oauth2/requestToken'
        )
    ),
    'tokens' => array(
    	'google' => 'AIzaSyD8UTiWwLri3M3xUkmNP6bUoTmv23ElWY8',
    	'bitly' => '54976fe798b38d5145a1e00af926ffff1a0f2d35'
    )
);
