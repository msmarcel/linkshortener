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

class GoogleController extends AbstractActionController
{

    protected $client;

    protected function init()
    {
        $uri = $this->getRequest()->getUri();
        $redirectUri = $uri->getScheme() . '://' . $uri->getHost();
        if ($uri->getScheme() == 'http' && $uri->getPort() != 80) {
            $redirectUri .= ':' . $uri->getPort();
        } elseif ($uri->getScheme() == 'https' && $uri->getPort() != 443) {
            $redirectUri .= ':' . $uri->getPort();
        }
        $redirectUri .= $this->url()->fromRoute('google/auth/complete');
        
        $this->client = new OAuth2Client(array(
            'client' => array(
                'client_id' => '116155492975-jiimc1pnbn8rutaif71tb9bf0udj7qn0.apps.googleusercontent.com',
                'client_secret' => '8LcgdVHWFdP--sQNFdcVqT1E',
                'scope' => 'https://www.googleapis.com/auth/urlshortener',
                'authorization_url' => 'https://accounts.google.com/o/oauth2/auth',
                'access_token_url' => 'https://accounts.google.com/o/oauth2/token',
                'redirect_uri' => $redirectUri
            )
        ));
        
        $this->client->setHttpClient(new HttpClient(array(
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false
            )
        )));
        
        $this->session = new Container('google');
    }

    public function indexAction()
    {
        $this->init();
        
        $url = $this->params()->fromQuery('url');
        $requestHeaders = array(
            'Content-Type' => 'application/json'
        );
        $needAuth = true;
        if ($this->session->accessToken) {
            $requestHeaders['Authorization'] = 'Bearer ' . $this->session->accessToken->getAccessToken();
            $needAuth = false;
        }
        $requestBody = json_encode(array(
            'key' => 'AIzaSyD8UTiWwLri3M3xUkmNP6bUoTmv23ElWY8',
            'longUrl' => $url
        ));
        
        $response = $this->client->post('https://www.googleapis.com/urlshortener/v1/url', null, $requestHeaders, $requestBody);
        
        $data = json_decode($response->getBody(), true);
        
        if (array_key_exists('id', $data)) {
            return new JsonModel(array_merge(array(
                'success' => true,
                'needauth' => $needAuth,
                'origurl' => $url,
                'shorturl' => $data['id']
            ), $data));
        }
        
        return new JsonModel(array_merge(array(
            'success' => false,
            'needauth' => $needAuth,
            'origurl' => $url
        ), $data));
    }

    public function authAction()
    {
        $this->init();
        
        return $this->redirect()->toUrl($this->client->getAuthorizationRequestUrl());
    }

    public function completeAction()
    {
        $this->init();
        
        $code = $this->params()->fromQuery('code');
        
        $this->session->accessToken = $this->client->getAccessToken(array(
            'code' => $code
        ));
        
        $response = array(
            'success' => true,
            'access_token' => $this->session->accessToken->getAccessToken()
        );
        
        return new JsonModel($response);
    }
}
