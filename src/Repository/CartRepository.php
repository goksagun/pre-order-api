<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function findCarts($hydrationMode = Query::HYDRATE_ARRAY)
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult($hydrationMode);
    }

    public function findCart($id, $hydrationMode = Query::HYDRATE_ARRAY)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult($hydrationMode);
    }

    public function insertCart($type)
    {
        $em = $this->getEntityManager();

        $cart = new Cart();
        $cart->setType($type);

        $em->persist($cart);
        $em->flush();

        return $cart;
    }

    public function updateCart($id, $type)
    {
        $em = $this->getEntityManager();

        /** @var Cart $cart */
        $cart = $em->getReference(Cart::class, $id);

        $cart->setType($type);

        $em->flush();

        return $cart;
    }

    public function deleteCart($id)
    {
        $em = $this->getEntityManager();

        $cart = $em->getReference(Cart::class, $id);

        $em->remove($cart);
        $em->flush();
    }
}
