<?php

namespace GenericBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use GenericBundle\Entity\Qcmdef;
use GenericBundle\Entity\Questiondef;
use GenericBundle\Entity\Reponsedef;

class Hub3eInitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hub3e:init')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $nomQCM = 'QCMparDéfault';
        $affinte = true;
        $questionsArray = ['Statut de votre société','Taille de votre société','Votre secteur d\'activité','Vos horaires de travail','Relations de l\'apprenant avec ses collaborateurs et sa hiérarchie',
            'Relation du tuteur , maître de stage , MAP avec son apprenant','Types de missions proposées à l\'apprenant','Type de contrat proposé à l\'apprenant','Rémunération proposée à l\'apprenant',
            'Mobilité de l\'apprenant par rapport à votre siège, bureaux , chantier','Déplacements de l\'apprenant liés à la mission','Axe / Stratégie de développement de votre société',
            'Possibilité d\'embauche ou de Poursuite de contrat pour l\'apprenant'];
        $reponseArray = array(['Startup','TPE / PME','Groupe','Institution publique','ONG / Association','Ecole / Centre de formation'],
            ['Moins de 10 salariés','Moins de 100 salariés','Plus de 100 salariés'],
            ['Primaire ( Agriculture, Elevage ... )','Secondaire ( Artisanat, Industrie ...)','Tertiaire ( Commerce , Finance , Transport, Services, Education, Santé ...)','Quaternaire ( Informatique , Nouvelles technologies ... )'],
            ['Horaires fixes ( 8h30-12h ; 14h-17h30 )','Horaires "flexibles" ( Deadlines à respecter )'],
            ['Apprenant orienté équipe ( Equipe soudée )','Apprenant orienté tâche ( Equipe de travail )','Apprenant attaché à la hiérarchie ( Equipe structurée )'],
            ['Personne nécessitant un accompagnement quotidien','Personne semi-autonome' , 'Personne totalement autonome'],
            ['Missions structurées ( Liée à la formation de l\'apprenant )','Missions évolutives ( Liées à vos besoins / polyvalence de l\'apprenant )','Missions proposée par l\'apprenant fort de propositions et d\'innovations' ],
            ['Stage','Stage alterné','Contrat d\'apprentissage','Contrat de professionnalisation' ],
            ['Entre 1 et 500 €','Entre 500 et 1000 €','Plus de 1000 €','Prise en charge des frais de scolarité et rémunération'],
            ['Moins de 10 minutes','Moins de 30 minutes','Moins de 1 heure'],
            ['Non ( aucun déplacement )','Possible ( 1 fois par mois/trimestre )','Fréquents ( 1 fois par semaine ou plus )'],
            ['Internationalisation','Résponsabilité sociétale et environnementale','Optimisation de la productivité','Innovation'],
            ['Oui','Non','Peut être selon le profil']);

        $scoreArray = array(['8','8','8','8','8','8','8'],
            ['8','8','8'],
            ['8','8','8','8'],
            ['8','8'],
            ['7','6','7'],
            ['7','7','7'],
            ['6','6','6'],
            ['10','10','10','10'],
            ['6','6','6','6'],
            ['6','6','6'],
            ['6','6','6'],
            ['6','6','6','6'],
            ['10','10','10']);

        //get qcm s'il existe.
        $qcm = $em->getRepository('GenericBundle:Qcmdef')->findOneBy(array('nom'=>$nomQCM));
        if(!$qcm)
        {
            //s'il n'existe pas le créer.
            $newqcm = new Qcmdef();
            $newqcm->setNom($nomQCM);

            $em->persist($newqcm);
            $em->flush();

            $qcm= $newqcm;
        }
        $qcm->setAffinite($affinte);
        $em->flush();

        $questions = array();

        $questionorder = 0;
        if($questionsArray)
        {
            foreach($questionsArray as $value)
            {
                //get Question s'elle existe.
                $question = $em->getRepository('GenericBundle:Questiondef')->findOneBy(array('question'=>$value,'qcmdef'=>$qcm));
                if(!$question)
                {
                    //S'elle n'existe pas la créer.
                    $newquestion = new Questiondef();
                    $newquestion->setQuestion($value);
                    $newquestion->setQcmdef($qcm);
                    $em->persist($newquestion);
                    $em->flush();
                    $question = $newquestion;
                }
                $question->setOrdre($questionorder++);
                $em->flush();
                array_push($questions,$question);

            }
            $i = 0;
            foreach( $reponseArray as $key => $reponses)
            {

                $reponseorder = 0;
                foreach($reponses as $repkey => $rep)
                {
                    //get Reponse s'elle existe.
                    $reponse = $em->getRepository('GenericBundle:Reponsedef')->findOneBy(array('reponse'=>$rep,'questiondef'=>$questions[$i]));

                    if(!$reponse)
                    {
                        //S'elle n'existe pas la créer.
                        $newreponse = new Reponsedef();
                        $newreponse->setReponse($rep);
                        $newreponse->setScore($scoreArray[$key][$repkey]);
                        $newreponse->setQuestiondef($questions[$i]);
                        $em->persist($newreponse);
                        $em->flush();
                        $reponse = $newreponse;
                    }
                    $reponse->setOrdre($reponseorder++);
                    $em->flush();

                }
                $i++;

            }


        }

        $output->writeln('Command executed.');
    }

}
