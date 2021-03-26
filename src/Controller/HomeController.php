<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Suivi;
use App\Repository\SuiviRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\Jwt\StaticJwtProvider;
class HomeController extends AbstractController
{
    private $suiviRepository;
    public function __construct(SuiviRepository $repo)
    {
        $this->suiviRepository = $repo;
    }
    /**
     * @Route("/users/home/{id}", name="users_home")
     */
    public function index($id = 0): Response
    {
        return $this->render('home/index.html.twig', [
            'datas' => $this->renderData($id),
            'duplicate' => ""
        ]);
    }

    /**
     * @Route("/users/home/entree/{id}", name="suivi_entree")
     */

    public function suivi_entree($id)
    {
        $suivi = $this->suiviRepository->findOneBy(["date" => date('d-m-Y'),"id_user" => $id]);
        if(empty($suivi))
        {
            $this->suiviRepository->suiviEntree($id,0,"En cours...");
            return $this->redirectToRoute("users_home",["id" => $id]);
        }
        else{
        return $this->render('home/index.html.twig', [
            'datas' => $this->renderData($id),
            'duplicate' => "Vous êtes déjà au bureau!"
        ]);
        }
    }

    /**
     * @Route("/users/home/goto/{id}", name="go_to")
     */
    public function goTo($id)
    {
        $dupl = $this->suiviRepository->findOneBy(["date" => date('d-m-Y'),"id_user" => $id, "status" => "Présent"]);
        $suivi = $this->suiviRepository->findOneBy(["date" => date('d-m-Y'),"id_user" => $id]);
        if(empty($dupl))
        {
            if(empty($suivi))
            {
                return $this->render('home/index.html.twig', [
                'datas' => $this->renderData($id),
                'duplicate' => "Vous n'êtes pas encore au bureau!"
                ]);
            }
            else
            {
            return $this->render('home/obs.html.twig', [
            'id' => $id
            ]);
            }
        }
        else{
                return $this->render('home/index.html.twig', [
                'datas' => $this->renderData($id),
                'duplicate' => "Vous êtes déjà rentré!"
                ]);
        }
    }
    /**
     * @Route("/users/home/sortie/{id}", name="suivi_sortie")
     */

    public function suivi_sortie($id, Request $request)
    {
        $obs = $request->get('_observation');
        $dupl = $this->suiviRepository->findOneBy(["date" => date('d-m-Y'),"id_user" => $id, "status" => "Présent"]);
        $suivi = $this->suiviRepository->findOneBy(["date" => date('d-m-Y'),"id_user" => $id]);
        if(empty($dupl))
        {
            if(empty($suivi))
            {
                return $this->render('home/index.html.twig', [
                'datas' => $this->renderData($id),
                'duplicate' => "Vous n'êtes pas encore au bureau!"
                ]);
            }
            else
            {
                $end = date('d-m-Y H:i:s');
                $start = $suivi->getHeure_Entree();
                $start_ = new \DateTime($start);
                $end_ = new \DateTime($end);
                $interval = $start_->diff($end_);
                $total = $interval->format('%h'). ":".$interval->format('%i');
                $obs = $obs;
                $this->suiviRepository->suiviSortie($suivi,$end,$total,$obs);
                return $this->redirectToRoute("users_home",["id" => $id]);
            }
        }
        else{
                return $this->render('home/index.html.twig', [
                'datas' => $this->renderData($id),
                'duplicate' => "Vous êtes déjà rentré!"
                ]);
        }
    }

    private function renderData($id)
    {
        $suivis = $this->suiviRepository->findBy(['id_user' => $id]);
        $data = [];
        foreach ($suivis as $suivi) {
            $data[] = [
                "id" => $suivi->getId(),
                "date" =>$suivi->getdate(),
                "heure_entree" =>$suivi->getHeure_Entree(),
                "heure_sortie" =>$suivi->getHeure_Sortie(),
                "total" =>$suivi->getTotal(),
                "observation" =>$suivi->getObservation()
            ];
        }
        return $data;
    }

    /**
     * @Route("/users/home/notif/", name="notification")
     */

     public function notification()
     {

        
         return $this->redirectToRoute("presence");
     }
}
