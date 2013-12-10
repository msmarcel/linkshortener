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

class TwitterController extends AbstractServiceController
{

    protected $name = 'twitter';

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
}
