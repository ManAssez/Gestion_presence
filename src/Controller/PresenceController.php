<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SuiviRepository;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
class PresenceController extends AbstractController
{
    private $presenceRepository;

    public function __construct(SuiviRepository $repo)
    {
        $this->presenceRepository = $repo;
    }
    /**
     * @Route("/admin/presence", name="presence")
     */
    public function index(): Response
    {
        return $this->render('presence/index.html.twig', [
            'datas' => $this->getData(1)
        ]);
    }


    /**
     * @Route("/admin/presence/excel/{id}", name="create_excel")
     */
    public function createExcel($id)
    {
        $spreadsheet = new Spreadsheet();
        $fileName = "Présence.xlsx";
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Liste de présence');
        $sheet->getCell('A1')->setValue('N° Mle');
        $sheet->getCell('B1')->setValue('Nom & Prénoms');
        $sheet->getCell('C1')->setValue('Département');
        $sheet->getCell('D1')->setValue('Date');
        $sheet->getCell('E1')->setValue('Entrée');
        $sheet->getCell('F1')->setValue('Sortie');
        $sheet->getCell('G1')->setValue('Durée');
        $sheet->getCell('H1')->setValue('Observation');

        // Increase row cursor after header write
        $sheet->fromArray($this->getData(2),null, 'A2', true);
        
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');

        $writer->save('php://output');
        return $this->redirectToRoute("presence");
    }
    private function getData($type)
    {
         $em = $this->getDoctrine()->getManager();

        $RAW_QUERY = 'SELECT 
            personal.nom as nom,
            personal.username as username,
            personal.prenom as prenom,
            personal.matricule as matricule,
            personal.fonction as fonction,
            suivi.date as daty,
            suivi.observation as observation,
            suivi.status as statu,
            suivi.heure_entree as heure_entree,
            suivi.heure_sortie as heure_sortie,
            suivi.total as total
         FROM personal INNER JOIN suivi ON personal.id = suivi.id_user';
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
        $data = [];
        $results = $statement->fetchAll();
        foreach ($results as $result) {
            if($type == 1)
            {
            $data[] = [
                "nom" => $result['nom'],
                "username" => $result['username'],
                "prenom" => $result['prenom'],
                "fonction" => $result['fonction'],
                "matricule" => $result['matricule'],
                "heure_entree" => $result['heure_entree'],
                "heure_sortie" => $result['heure_sortie'],
                "daty" => $result['daty'],
                "observation" => $result['observation'],
                "status" => $result['statu'],
                "total" => $result['total'],
            ];
            }
            else
            {
                $e=strtotime($result['heure_entree']);
                $s=strtotime($result['heure_sortie']);
                $data[] = [
                "matricule" => $result['matricule'],
                "nom" => $result['nom']." ".$result['prenom'],
                "fonction" => $result['fonction'],
                "daty" => $result['daty'],
                "heure_entree" => date("H:i:s", $e),
                "heure_sortie" => date("H:i:s", $s),
                "total" => $result['total'],
                "observation" => $result['observation']
            ];
            }
            
        }

        return $data;
    }
    /**
     * @Route("/admin/presence/test", name="test_excel")
     */
        public function indexAction()
    {
        // You only need to provide the path to your static file
        // $filepath = 'path/to/TextFile.txt';

        // i.e Sending a file from the resources folder in /web
        // in this example, the TextFile.txt needs to exist in the server
        $publicResourcesFolderPath = $this->get('kernel')->getRootDir() . '/../web/public-resources/';
        $filename = "TextFile.txt";

        // This should return the file to the browser as response
        $response = new BinaryFileResponse($publicResourcesFolderPath.$filename);

        // To generate a file download, you need the mimetype of the file
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        // Set the mimetype with the guesser or manually
        if($mimeTypeGuesser->isSupported()){
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guess($publicResourcesFolderPath.$filename));
        }else{
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'text/plain');
        }

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}
