<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Personal;
use App\Repository\PersonalRepository;

class UsersController extends AbstractController
{
    private $personalRepo;

    public function __construct(PersonalRepository $personal)
    {
        $this->personalRepo = $personal;
    }
    /**
     * @Route("/admin/lists", name="users_list")
     */
    public function index(): Response
    {
        $personals = $this->personalRepo->findAll();
        $data= [];

        foreach ($personals as $personal) {
                $data[] = [
                'id' => $personal->getId(),
                'Nom' => $personal->getNom(),
                'Prenom' => $personal->getPrenom(),
                'Fonction' => $personal->getFonction(),
                'Matricule' => $personal->getMatricule(),
                'DateCreated' => $personal->getDateCreated(),
                'Mac' => $personal->getMac(),
                'Type' => $personal->getType(),
                'Username' => $personal->getUsername(),
            ];
        }

        return $this->render('users/index.html.twig', [
            'datas' => $data
        ]);
    }


    /**
     * @Route("/admin/user/delete/{id}", name="users_delete")
     */
    public function deleteUser($id)
    {
        $personal = $this->personalRepo->findOneBy(['id'=> $id]);
        $this->personalRepo->deletePersonal($personal);

        return $this->redirectToRoute("users_list");
    }
}
