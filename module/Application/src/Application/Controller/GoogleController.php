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
use ZendService\Api\Api;
use ZendService\Oauth2\Client\Client as OAuth2Client;

class ShortenerController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function googleAction()
    {
        $oauthConsumer = new OAuth2Client(array(
            'client' => array(
                'client_id' => '116155492975-jiimc1pnbn8rutaif71tb9bf0udj7qn0.apps.googleusercontent.com',
                'client_secret' => '8LcgdVHWFdP--sQNFdcVqT1E',
                'authorization_url' => 'https://accounts.google.com/o/oauth2/auth',
                'request_token_url' => 'https://accounts.google.com/o/oauth2/token',
                'access_token_url' => 'https://accounts.google.com/o/oauth2/token',
                'redirect_uri' => '',
                'state' => ''
            )
        ));
        
        $token = $oauthConsumer->getRequestToken();
        return new JsonModel();
    }
}
