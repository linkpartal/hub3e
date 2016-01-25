<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use GenericBundle\Entity\Notification;
use GenericBundle\Entity\Modele;

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
        //$em->persist($newuser);
        //$em->flush();

        $superadmins = $this->getDoctrine()->getRepository('GenericBundle:User')->findByRole('ROLE_SUPER_ADMIN');

        foreach($superadmins as $admin){
            $notif = new Notification();
            $notif->setEntite($newuser->getId());
            $notif->setType('Utilisateur');
            $notif->setUser($admin);
            //$em->persist($notif);
            //$em->flush();
        }

        //send password
        if($request->get('_modele'))
        {

            $modele = 'GenericBundle:Mail/templates:'.$request->get('_modele').'_'.$this->get('security.token_storage')->getToken()->getUser()->getUsername().'.html.twig';
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

        if($request->get('_id'))
        {
            return $this->redirect($this->generateUrl('metier_user_affiche',array('id'=>$request->get('_id'))));
        }
        else
        {
            return $this->redirect($this->generateUrl('metier_user_admin'));
        }


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

    public function creeNewModeleAction($id)
    {
        /*
        $modeles = array();
        if ($handle = opendir('../src/GenericBundle/Resources/views/Mail')) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != "." && $entry != "..") {

                    array_push($modeles,$entry);
                }
            }

            closedir($handle);
        }*/
        if($id == 'ajouter')
        {
            return $this->render("UserBundle:Gestion:creeNewModele.html.twig");
        }
        else{
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $modele = $this->getDoctrine()->getRepository('GenericBundle:Modele')->find($id);
            $d = new \DOMDocument;
            @$d->loadHTML(file_get_contents('./templates/'. $modele->getId().'_'. $user->getUsername() .'.html.twig'));
            $body = "";
            foreach($d->getElementsByTagName("body")->item(0)->childNodes as $child) {
                $body .= $d->saveHTML($child);
            }

            return $this->render("UserBundle:Gestion:creeNewModele.html.twig",array('modele'=>$modele,'body'=>$body));
        }

    }
    public function saveNewModeleAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $modele = $this->getDoctrine()->getRepository('GenericBundle:Modele')->findOneBy(array('user'=>$user,'nom'=>$request->get('_filename')));
        if(!$modele)
        {
            $modele = new Modele();
            $modele->setNom($request->get('_filename'));
            $modele->setUser($user);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($modele);
            $em->flush();
        }

        $myfile = fopen("../src/GenericBundle/Resources/views/Mail/templates/". $modele->getId()."_". $user->getUsername() .".html.twig","w");

        fwrite($myfile,$this->render('GenericBundle:Mail:Modele.html.twig',array('Textarea'=>$request->get('_newtext')))->getContent());
        fclose($myfile);
        return $this->redirect($this->generateUrl('admin_iframeload'));
    }
}
