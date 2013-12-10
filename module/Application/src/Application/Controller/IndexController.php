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

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function servicesAction()
    {
        $uri = $this->getRequest()->getUri();
        $baseUri = $uri->getScheme() . '://' . $uri->getHost();
        if ($uri->getScheme() == 'http' && $uri->getPort() != 80) {
            $baseUri .= ':' . $uri->getPort();
        } elseif ($uri->getScheme() == 'https' && $uri->getPort() != 443) {
            $baseUri .= ':' . $uri->getPort();
        }
        
        return new JsonModel(array(
            array(
                'id' => 'google',
                'enabled' => true,
                'service' => 'Google (goo.gl)',
                'type' => 'get',
                'api' => $this->url()->fromRoute('google') . '?url=',
                'success' => true,
                'shortlink' => null,
                'showdescription' => false,
                'showtitle' => false,
                'showimage' => false,
                'auth' => 'allowed',
                'authlink' => $baseUri . $this->url()->fromRoute('google/auth'),
                'serviceicon' => "brandico-googleplus-rect"
            ),
            array(
                'id' => 'bitly',
                'enabled' => false,
                'service' => 'Bitly (bit.ly)',
                'type' => 'get',
                'api' => $this->url()->fromRoute('bitly') . '?url=',
                'success' => true,
                'shortlink' => null,
                'showdescription' => false,
                'showtitle' => false,
                'showimage' => false,
                'auth' => 'required',
                'authlink' => $baseUri . $this->url()->fromRoute('bitly/auth'),
                'serviceicon' => array(
                    "glyphicon",
                    "glyphicon-user"
                )
            ),
            array(
                'id' => 'twitter',
                'enabled' => false,
                'service' => 'Twitter (t.co)',
                'type' => 'get',
                'api' => $this->url()->fromRoute('twitter') . '?url=',
                'success' => true,
                'shortlink' => null,
                'showdescription' => false,
                'showtitle' => false,
                'showimage' => true,
                'auth' => 'required',
                'authlink' => $baseUri . $this->url()->fromRoute('twitter/auth'),
                'serviceicon' => "brandico-twitter-bird"
            )
        ));
    }
}
