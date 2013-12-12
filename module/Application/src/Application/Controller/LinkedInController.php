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

class LinkedInController extends AbstractServiceController
{

    protected $name = 'linkedin';

    protected function makeApiCall($url, $title, $description, $image)
    {
        if (isset($this->session->accessToken)) {
            $token = $this->session->accessToken->getAccessToken();
            
            $requestHeaders = array(
                'Content-Type' => 'application/json',
                'x-li-format' => 'json'
            );
            $requestData = array(
                'content' => array(
                    'submitted-url' => urldecode($url)
                ),
                'visibility' => array(
                    'code' => 'anyone'
                )
            );
            if ($title) {
                $requestData['content']['title'] = urldecode($title);
            }
            if ($description) {
                $requestData['content']['description'] = urldecode($description);
            }
            if ($image) {
                $requestData['content']['submitted-image-url'] = urldecode($image);
            }
            
            $requestBody = json_encode($requestData);
            $response = $this->client->post("https://api.linkedin.com/v1/people/~/shares?oauth2_access_token={$token}", null, $requestHeaders, $requestBody);
            $data = json_decode($response->getBody(), true);
            
            if (array_key_exists('updateKey', $data)) {
                $key = urlencode($data['updateKey']);
                $update = $this->client->get("https://api.linkedin.com/v1/people/~/network/updates/key={$key}", array(
                    'oauth2_access_token' => $token
                ), $requestHeaders, null);
                error_log($update);
                $extraData = json_decode($update->getBody(), true);
                return array(
                    'success' => true,
                    'needauth' => false,
                    'origurl' => $url,
                    'shorturl' => $data['updateKey'],
                    'data' => $data,
                    'extra' => $extraData
                );
            } else {
                return array(
                    'success' => false,
                    'needauth' => false,
                    'origurl' => $url,
                    'data' => $data
                );
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
