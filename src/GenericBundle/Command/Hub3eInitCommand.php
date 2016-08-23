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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $nomQCM = 'QCMparDéfault';
        $affinte = true;
        $questionsArray = [
            'Ma société est',
            'Mon secteur d\'activité est',
            'Dans mon entreprises les horaires sont',
            'Mon entreprise propose en standard',
            'Mon entreprise recherche un apprenant',
            'Le type de contrat que je propose',
            'Pour réaliser la mission, des déplassements sont',
            'Quelles est la qualités principale recherchée chez votre apprenant',
            'Quelles est la qualités secondaire recherchée chez votre apprenant',
            'Si la colaboration est positive, à la suite du contrat',

        ];
        $reponseArray = array(
            ['Startup',
                'TPE / PME',
                'Groupe',
                'Groupe Multinational',
                'Institution publique',
                'ONG / Association',
                'Ecole / Centre de formation'],
            ['Primaire ( Agriculture, Elevage ... )',
                'Secondaire ( Artisanat, Industrie ...)',
                'Tertiaire ( Commerce , Finance , Transport, Services, Education, Santé ...)',
                'Quaternaire ( Informatique , Nouvelles technologies ... )'],
            ['Horaires fixes ( 8h30-12h ; 14h-17h30 )',
                'Horaires "flexibles" ( Deadlines à respecter )',
                'Peu importe'],
            ['Tickets restaurant',
                'Comité d\'entreprise',
                'Matériel de fonction ( PC , Portable , Tablette .. )',
                'Véhicule de fonction / Carte essence / Télépéage',
                'Primes ( Participation , intéressement ...)',
                'Peu importe'],
            ['Personne nécessitant un accompagnement quotidien',
                'Personne semi-autonome',
                'Personne totalement autonome' ],
            ['Stage',
                'Stage alterné',
                'Contrat d\'apprentissage',
                'Contrat de professionnalisation',
                'Peu importe' ],
            ['Aucun déplacement',
                'Occasionnel',
                'Fréquents',
                'Peu importe'],
            ['Le goût du challenge',
                'Son Autonomie / Maturité',
                'Son dynamisme'],
            ['Ouvert d\'esprit',
                'Authentique / Loyal',
                'Grand sens du relationnel',
                'Adaptabilité / Polyvalence'],
            ['Je peux proposer une poursuite',
                'Je préfère former un autre apprenant',
                'Peu importe']);

        $scoreArray = array(
            ['8','8','8','8','8','8','8'],
            ['8','8','8','8'],
            ['8','8','8'],
            ['8','8','8','8','8','8'],
            ['7','7','7'],
            ['6','6','6','6','6'],
            ['6','6','6','6'],
            ['6','6','6'],
            ['6','6','6','6'],
            ['6','6','6']);

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
