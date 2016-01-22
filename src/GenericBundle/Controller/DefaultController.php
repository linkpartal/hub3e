<?php

namespace GenericBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function checkAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername( $request->get('_username'));

        if (!$user) {

            return $this->forward('GenericBundle:Default:loadlogin');

        }
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        if($encoder->isPasswordValid($user->getPassword(),$request->get('_password') ,$user->getSalt()))
        {
            if(!$user->getLastLogin())
            {
               return $this->redirect('change-password/'.$request->get('_username'));
            }
            $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
            $context = $this->get('security.token_storage');
            $context->setToken($token);
            //Change Last Login to Now
            $date = new \DateTime();
            $user->setLastLogin($date);
            $em->flush();
            if (true === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
            {
                return $this->redirect('admin');
            }
            elseif(true === $this->get('security.authorization_checker')->isGranted('ROLE_ADMINECOLE'))
            {
                $ecole = $em->getRepository('GenericBundle:Tier')->find($user->getTier());
                return $this->redirect($this->generateUrl('ecole_admin',array('ecole'=>$ecole->getRaisonSoc())));
            }
            elseif(true === $this->get('security.authorization_checker')->isGranted('ROLE_USER'))
            {
                return $this->redirect('http://youtube.com');
            }
            else{
                var_dump($user);
                die();
            }
        }
        else
        {
            return $this->forward('GenericBundle:Default:loadlogin');
        }




    }

    public function loadloginAction(){
        return $this->redirect('login');
    }

    public function changePasswordAction($username)
    {
        return $this->render('GenericBundle:ChangePassword:changePassword.html.twig',array('username'=>$username));
    }

    public function passwordChangedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername( $request->get('_username'));

        $hash =  $this->get('security.password_encoder')->encodePassword($user, $request->get('_password'));
        $user->setPassword($hash);

        //Change Last Login to Now
        $date = new \DateTime();
        $user->setLastLogin($date);
        $em->flush();

        /*
        //connect to Symfony
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $context = $this->get('security.token_storage');
        $context->setToken($token);*/

        return $this->redirect('login');

    }

    public function adressesAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $etablissement = $em->getRepository('GenericBundle:Etablissement')->find($id);
        $etablissements = $em->getRepository('GenericBundle:Etablissement')->findBy(array('tier'=>$etablissement->getTier()));
        $adresses = array();
        foreach($etablissements as $value)
        {
            $adresse = array('id'=>$value->getId(),'adresse' => $value->getAdresse());
            array_push($adresses, json_encode($adresse) );
        }
        $reponse = new JsonResponse();
        return $reponse->setData(array('adresses'=>$adresses));
    }

    public function expiredAction($id){
        $utilis = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);
        $reponse = new JsonResponse();
        if($utilis->isExpired())
        {
            $utilis->setExpired(false);
            $reponse->setData(array('succes'=>'0'));
        }
        else{
            $utilis->setExpired(true);
            $reponse->setData(array('succes'=>'1'));
        }
        $this->getDoctrine()->getEntityManager()->flush();

        return $reponse;
    }
}
