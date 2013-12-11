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
use ZendOAuth\Consumer;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Curl;

class TwitterController extends AbstractServiceController
{

    protected $name = 'twitter';

    protected function init()
    {
        $config = $this->getServiceLocator()->get('Config');
        
        if (! isset($this->name)) {
            throw new Exception('Service name must be set!');
        }
        
        $uri = $this->getRequest()->getUri();
        $baseUri = $uri->getScheme() . '://' . $uri->getHost();
        if ($uri->getScheme() == 'http' && $uri->getPort() != 80) {
            $baseUri .= ':' . $uri->getPort();
        } elseif ($uri->getScheme() == 'https' && $uri->getPort() != 443) {
            $baseUri .= ':' . $uri->getPort();
        }
        $this->authUri = $baseUri . $this->url()->fromRoute($this->name . '/auth');
        $redirectUri = $baseUri . $this->url()->fromRoute($this->name . '/auth/complete');
        
        $this->client = new Consumer(array_merge($config['link_services'][$this->name], array(
            'callbackUrl' => $redirectUri
        )));
        
        $curl = new Curl();
        $curl->setOptions(array(
            'curloptions' => array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false
            )
        ));
        
        $http = $this->client->getHttpClient();
        $http->setAdapter($curl);
        
        $this->session = new Container($this->name);
    }

    protected function makeApiCall($url)
    {
        if (isset($this->session->accessToken)) {
            $requestParams = array(
                'access_token' => $this->session->accessToken->getAccessToken(),
                'longUrl' => $url
            );
            
            $response = $this->client->get('https://api.twitter.com/', $requestParams);
            $data = json_decode($response->getBody(), true);
            
            if (array_key_exists('url', $data['data'])) {
                return array_merge(array(
                    'success' => true,
                    'needauth' => false,
                    'origurl' => $url,
                    'shorturl' => $data['data']['url']
                ), $data);
            } else {
                return array_merge(array(
                    'success' => false,
                    'needauth' => false,
                    'origurl' => $url,
                    'shorturl' => $data['status_txt']
                ), $data);
            }
        }
        
        return array(
            'success' => false,
            'needauth' => true,
            'authlink' => $this->authUri,
            'origurl' => $url
        );
    }

    public function authAction()
    {
        $this->init();
        
        $token = $this->client->getRequestToken();
        $_SESSION['TWITTER_REQUEST_TOKEN'] = serialize($token);
        
        return $this->redirect()->toUrl($this->client->getRedirectUrl());
    }

    public function completeAction()
    {
        $this->init();
        
        $this->session->accessToken = unserialize($_SESSION['TWITTER_ACCESS_TOKEN']);
        unset($_SESSION['TWITTER_ACCESS_TOKEN']);
        
        return $this->redirect()->toRoute('home');
    }
}
