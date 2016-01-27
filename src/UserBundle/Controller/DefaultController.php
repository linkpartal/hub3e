<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use GenericBundle\Entity\Notification;
use GenericBundle\Entity\Modele;
use Ddeboer\DataImport\Reader\CsvReader;

class DefaultController extends Controller
{
    public function affichageUserAction($id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $userid = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);
        $type = 'Utilisateur';
        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findOneBy(array('user'=>$user,'entite'=>$userid->getId(),'type'=>$type));
        if($notifications)
        {
            $this->getDoctrine()->getEntityManager()->remove($notifications);
            $this->getDoctrine()->getEntityManager()->flush();
        }


        return $this->render('UserBundle:Gestion:iFrameContentUser.html.twig',array('licencedef'=>$licencedef,'User'=>$userid
        ));
    }

    public function UserAddedAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $newuser = $userManager->createUser();
        $newuser->setUsername($request->get('_Username'));
        $newuser->setEmail($request->get('_mail'));
        $newuser->addRole($request->get('_role'));
        $newuser->setCivilite($request->get('civilite'));
        $newuser->setTelephone($request->get('_Tel'));
        $newuser->setPrenom($request->get('_Prenom'));
        $newuser->setNom($request->get('_Nom'));

        //generate a password
        $tokenGenerator = $this->get('fos_user.util.token_generator');
        $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
        $hash =  $this->get('security.password_encoder')->encodePassword($newuser, $password);
        $newuser->setPassword($hash);


        if($request->get('_role')=='ROLE_ADMINSOC' || $request->get('_role')=='ROLE_ADMINECOLE')
        {
            $etab = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_id'));
            $newuser->setTier($etab->getTier());
        }
        if($request->get('_role')=='ROLE_RECRUTE' || $request->get('_role')=='ROLE_TUTEUR')
        {
            $etab = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_id'));
            $newuser->setEtablissement($etab);
        }


        $em = $this->getDoctrine()->getManager();
        $em->persist($newuser);
        $em->flush();

        $superadmins = $this->getDoctrine()->getRepository('GenericBundle:User')->findByRole('ROLE_SUPER_ADMIN');

        foreach($superadmins as $admin){
            $notif = new Notification();
            $notif->setEntite($newuser->getId());
            $notif->setType('Utilisateur');
            $notif->setUser($admin);
            $em->persist($notif);
            $em->flush();
        }

        //send password
        $usercon = $this->get('security.token_storage')->getToken()->getUser();

        if($usercon->getTier())
        {
            if($this->get('templating')->exists('GenericBundle:Mail/templates:'.$usercon->getTier()->getSiren().'_NewUser.html.twig'))
            {
                $modele = 'GenericBundle:Mail/templates:'.$usercon->getTier()->getSiren().'_NewUser.html.twig';
            }
            else{
                $modele = 'GenericBundle:Mail:NewUser.html.twig';
            }
        }
        elseif($usercon->getEtablissement())
        {
            if($this->get('templating')->exists('GenericBundle:Mail/templates:'.$usercon->getEtablissement()->getTier()->getSiren().'_NewUser.html.twig'))
            {
                $modele = 'GenericBundle:Mail/templates:'.$usercon->getEtablissement()->getTier()->getSiren().'_NewUser.html.twig';
            }
            else{
                $modele = 'GenericBundle:Mail:NewUser.html.twig';
            }
        }
        else{
            $modele = 'GenericBundle:Mail:NewUser.html.twig';
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Email')
            ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
            ->setTo($request->get('_mail'))
            ->setBody($this->renderView($modele,array('username'=>$request->get('_Username'), 'password'=>$password))
                ,'text/html'
            );
        $this->get('mailer')->send($message);

        if($request->get('_id') and $usercon->hasRole('ROLE_SUPER_ADMIN'))
        {
            return $this->redirect($this->generateUrl('metier_user_affiche',array('id'=>$request->get('_id'))));
        }
        elseif($usercon->hasRole('ROLE_SUPER_ADMIN'))
        {
            return $this->redirect($this->generateUrl('metier_user_admin'));
        }
        elseif($request->get('_id') and $usercon->hasRole('ROLE_ADMINECOLE'))
        {
            return $this->redirect($this->generateUrl('ecole_admin_affiche',array('id'=>$request->get('_id'))));
        }
        else{
            throw new Exception('ERROR');
        }
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

    public function modifierAction($id)
    {
        $userid = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);
        return $this->render('UserBundle:Gestion:modifierUtilisateur.html.twig',array('user'=>$userid));
    }

    public function userModifAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('GenericBundle:User')->findOneBy(array('id'=>$request->get('_ID')));

        $user->setCivilite($request->get('_Civilite'));
        $user->setNom($request->get('_Nom'));
        $user->setPrenom($request->get('_Prenom'));
        $user->setTelephone($request->get('_Tel'));
        $user->setUsername($request->get('_Username'));
        $user->setEmail($request->get('_Mail'));

        $em->flush();

        return $this->forward('UserBundle:Default:affichageUser',array('id'=>$request->get('_ID')));
    }

    public function supprimeruserAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('GenericBundle:User')->find($id);

        if(!$user)
        {
            throw new Exception('Aucun utilisateur ne posséde l\'id ' . $id);
        }


        $em->remove($user);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Succes'=>$this->generateUrl('')));
    }

    public function importAction(Request $request)
    {


        $file = new \SplFileObject($_FILES['_CSV']['tmp_name']);
        $reader = new CsvReader($file);
        $reader->setHeaderRowNumber(0,CsvReader::DUPLICATE_HEADERS_INCREMENT);
        foreach ($reader as $row) {
            var_dump($row);
        }
        die;
    }
}
