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
                'serviceicon' => "brandico-googleplus-rect"
            ),
            array(
                'id' => 'bitly',
                'enabled' => true,
                'service' => 'Bitly (bit.ly)',
                'type' => 'get',
                'api' => $this->url()->fromRoute('bitly') . '?url=',
                'success' => true,
                'shortlink' => null,
                'showdescription' => false,
                'showtitle' => false,
                'showimage' => false,
                'auth' => 'required',
                'serviceicon' => array(
                    "glyphicon",
                    "glyphicon-user"
                )
            )
        ));
    }
}
