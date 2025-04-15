<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

<<<<<<< HEAD
    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
=======
    public function findAll(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT u FROM App\Entity\User u');
        return $query->getResult();
    }

    public function findOneById(int $id): User
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT u FROM App\Entity\User u WHERE u.id = :id')->setParameter('id', $id);
        return $query->getSingleResult();
    }

    public function create(User $user): User
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $user;
    }

    public function update(User $user): User
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $user;
    }

    public function delete(User $user): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($user);
        $entityManager->flush();
    }
>>>>>>> Release/1.0.0
}
