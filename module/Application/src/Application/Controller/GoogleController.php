<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;
use ZendService\Api\Api;
use ZendService\Oauth2\Client\Client as OAuth2Client;
use Application\OAuth\HttpClient;

class GoogleController extends AbstractServiceController
{
    
    protected $name = 'google';

    protected function makeApiCall($url)
    {
        $requestHeaders = array(
            'Content-Type' => 'application/json'
        );
        $needAuth = true;
        if ($this->session->accessToken) {
            $requestHeaders['Authorization'] = 'Bearer ' . $this->session->accessToken->getAccessToken();
            $needAuth = false;
        }
        
        $config = $this->getServiceLocator()->get('Config');
        $requestBody = json_encode(array(
            'key' => $config['tokens']['google'],
            'longUrl' => $url
        ));
        
        $response = $this->client->post('https://www.googleapis.com/urlshortener/v1/url', null, $requestHeaders, $requestBody);
        
        $data = json_decode($response->getBody(), true);
        
        if (array_key_exists('id', $data)) {
            return array_merge(array(
                'success' => true,
                'needauth' => $needAuth,
                'authlink' => $this->authUri,
                'origurl' => $url,
                'shorturl' => $data['id']
            ), $data);
        }
        
        return array_merge(array(
            'success' => false,
            'needauth' => $needAuth,
            'authlink' => $this->authUri,
            'origurl' => $url
        ), $data);
    }
}
