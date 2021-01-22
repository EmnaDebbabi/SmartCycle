<?php
/**
 * Created by PhpStorm.
 * User: debba
 * Date: 3/25/2019
 * Time: 11:31 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    /**
     * @Route("/add")
     */
    public function addAction()
    {

    }
    /**
     * @Route("/home")
     */
    public function redirectAction()
    {
        $authChecker = $this->container->get('security.authorization_checker');
        if($authChecker->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('admin_homepage');

        }else  if($authChecker->isGranted('ROLE_USER')){
            return $this->redirectToRoute('user_homepage');
        }else {
            return $this->render('@FOSUser/Security/login.html.twig');

        }
    }
}