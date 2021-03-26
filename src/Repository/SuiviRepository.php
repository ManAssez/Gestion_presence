<?php

namespace App\Repository;

use App\Entity\Suivi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Suivi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suivi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suivi[]    findAll()
 * @method Suivi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiviRepository extends ServiceEntityRepository
{

    private $manager;
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $manager)
    {
        parent::__construct($registry, Suivi::class);
        $this->manager = $manager;
    }



    public function suiviEntree($id_user,$total,$status)
    {
        $suivi = new Suivi();
        
        $suivi->setId_User($id_user)
        ->setTotal($total)
        ->setStatus($status)
        ->setDate(date('d-m-Y'))
        ->setHeure_Entree(date('d-m-Y H:i:s'))
        ->setObservation("")
        ->setHeure_Sortie("");
        $this->manager->persist($suivi);
        $this->manager->flush();
    }

    public function suiviSortie(Suivi $suivi, $date,$total,$observation)
    {
        $suivi->setHeure_Sortie($date)
        ->setStatus("Présent")
        ->setObservation($observation)
        ->setTotal($total);
        $this->manager->flush();
    }


    // /**
    //  * @return Suivi[] Returns an array of Suivi objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Suivi
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
