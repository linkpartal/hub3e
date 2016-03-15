<?php

namespace GenericBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Hub3eEmailingCompleteCompteRenduCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hub3e:emailing:completeCompteRendu')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $rendezvous1 = $em->getRepository('GenericBundle:RDV')->findBy(array('statut'=>1));
        foreach($rendezvous1 as $rdv){
            if($rdv->getDate1() < date_create('-1hours') ){
                $output->writeln('envoi mail à '. $rdv->getApprenant()->getUsername(). ' et '.$rdv->getTuteur()->getUsername());

                $message = \Swift_Message::newInstance()
                    ->setSubject('Email')
                    ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                    ->setTo($rdv->getApprenant()->getEmail())
                    ->setBody($this->getContainer()->get('templating')->render('GenericBundle:Mail:EmailRemplirCompteRendu.html.twig',array('rdv'=>$rdv,'tuteur'=>false))
                        ,'text/html'
                    );
                $this->getContainer()->get('mailer')->send($message);
                $message = \Swift_Message::newInstance()
                    ->setSubject('Email')
                    ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                    ->setTo($rdv->getTuteur()->getEmail())
                    ->setBody($this->getContainer()->get('templating')->render('GenericBundle:Mail:EmailRemplirCompteRendu.html.twig',array('rdv'=>$rdv,'tuteur'=>true))
                        ,'text/html'
                    );
                $this->getContainer()->get('mailer')->send($message);
            }
        }
        $rendezvous2 = $em->getRepository('GenericBundle:RDV')->findBy(array('statut'=>2));
        foreach($rendezvous2 as $rdv){
            $CRapprenant = $em->getRepository('GenericBundle:CompteRendu')->findBy(array('auteur'=>$rdv->getApprenant(),'rendezvous'=>$rdv));
            $CRtuteur = $em->getRepository('GenericBundle:CompteRendu')->findBy(array('auteur'=>$rdv->getTuteur(),'rendezvous'=>$rdv));
            if(count($CRapprenant) > count($CRtuteur) ){
                $message = \Swift_Message::newInstance()
                    ->setSubject('Email')
                    ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                    ->setTo($rdv->getTuteur()->getEmail())
                    ->setBody($this->getContainer()->get('templating')->render('GenericBundle:Mail:EmailRemplirCompteRendu.html.twig',array('tuteur'=>true,'rdv'=>$rdv))
                        ,'text/html'
                    );
                $this->getContainer()->get('mailer')->send($message);
            }
            elseif(count($CRapprenant) < count($CRtuteur)){
                $message = \Swift_Message::newInstance()
                    ->setSubject('Email')
                    ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                    ->setTo($rdv->getApprenant()->getEmail())
                    ->setBody($this->getContainer()->get('templating')->render('GenericBundle:Mail:EmailRemplirCompteRendu.html.twig',array('tuteur'=>false,'rdv'=>$rdv))
                        ,'text/html'
                    );
                $this->getContainer()->get('mailer')->send($message);
            }
        }
        if ($input->getOption('option')) {
            // ...
        }
    }

}
