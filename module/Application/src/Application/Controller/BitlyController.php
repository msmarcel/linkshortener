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
use Application\OAuth\Client as OAuth2Client;
use Application\OAuth\HttpClient;

class BitlyController extends AbstractActionController
{

    protected $client;

    protected function init()
    {
        $uri = $this->getRequest()->getUri();
        $baseUri = $uri->getScheme() . '://' . $uri->getHost();
        if ($uri->getScheme() == 'http' && $uri->getPort() != 80) {
            $baseUri .= ':' . $uri->getPort();
        } elseif ($uri->getScheme() == 'https' && $uri->getPort() != 443) {
            $baseUri .= ':' . $uri->getPort();
        }
        $this->authUri = $baseUri . $this->url()->fromRoute('bitly/auth');
        $redirectUri = $baseUri . $this->url()->fromRoute('bitly/auth/complete');
        
        $this->client = new OAuth2Client(array(
            'client' => array(
                'client_id' => 'b4ae6ba623cf7409f817389d9b619ba73dba379a',
                'client_secret' => 'c84e8ace2b37b55cd0e93763fb773c4dbcd3ff4d',
                'authorization_url' => 'https://bitly.com/oauth/authorize',
                'access_token_url' => 'https://api-ssl.bitly.com/oauth/access_token',
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
        
        $this->session = new Container('bitly');
    }

    public function indexAction()
    {
        $this->init();
        
        $url = $this->params()->fromQuery('url');
        if (isset($this->session->accessToken)) {
            $requestParams = array(
                'access_token' => $this->session->accessToken->getAccessToken(),
                'longUrl' => $url
            );
            
            $response = $this->client->get('https://api-ssl.bitly.com/v3/shorten', $requestParams);
            $data = json_decode($response->getBody(), true);
            
            if (array_key_exists('url', $data['data'])) {
                return new JsonModel(array_merge(array(
                    'success' => true,
                    'needauth' => false,
                    'origurl' => $url,
                    'shorturl' => $data['data']['url']
                ), $data));
            } else {
                return new JsonModel(array_merge(array(
                	'success' => false,
                    'needauth' => false,
                    'origurl' => $url,
                    'shorturl' => $data['status_txt']
                ), $data));
            }
        }
        
        return new JsonModel(array(
            'success' => false,
            'needauth' => true,
            'authlink' => $this->authUri,
            'origurl' => $url
        ));
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
