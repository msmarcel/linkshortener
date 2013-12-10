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

abstract class AbstractServiceController extends AbstractActionController
{

    protected $name;

    protected $client;

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
        
        $this->client = new OAuth2Client(array(
            'client' => array_merge($config['link_services'][$this->name], array(
                'redirect_uri' => $redirectUri
            ))
        ));
        
        $this->client->setHttpClient(new HttpClient(array(
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false
            )
        )));
        
        $this->session = new Container($this->name);
    }

    protected function makeApiCall($url)
    {
        throw new Exception('makeApiCall must be defined by subclass!');
    }

    public function indexAction()
    {
        $this->init();
        
        $url = $this->params()->fromQuery('url');
        $response = $this->makeApiCall($url);
        
        if ($response) {
            return new JsonModel($response);
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
        
        return $this->redirect()->toRoute('home');
    }

    public function authCheckAction()
    {
        $this->init();
        
        return new JsonModel(array(
            'status' => (isset($this->session->accessToken) ? 'loggedin' : 'loggedout')
        ));
    }

    public function logoutAction()
    {
        $this->init();
        
        $this->session->accessToken = null;
        
        return $this->redirect()->toRoute('home');
    }
}
