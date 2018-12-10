<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CartProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartProduct[]    findAll()
 * @method CartProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CartProduct::class);
    }

    public function findByCartId($cartId, $hydrationMode = Query::HYDRATE_ARRAY)
    {
        return $this->createQueryBuilder('c')
            ->addSelect('partial p.{id,sku,name,stock,description}')
            ->innerJoin('c.product', 'p')
            ->andWhere('c.cart = :cartId')
            ->setParameter('cartId', $cartId)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult($hydrationMode);
    }

    public function insertCartProduct($cartId, $type, $quantity, $product)
    {
        $em = $this->getEntityManager();

        /** @var Cart $cart */
        $cart = $em->getReference(Cart::class, $cartId);

        $cartProduct = new CartProduct();
        $cartProduct->setProduct(
            $em->getReference(Product::class, $product['id'])
        );
        $cartProduct->setType($type);
        $cartProduct->setQuantity($quantity);

        $cart->addItem($cartProduct);

        $em->persist($cartProduct);
        $em->flush();

        return $cartProduct;
    }

    public function updateCartProduct($id, $type, $quantity, $product)
    {
        $em = $this->getEntityManager();

        /** @var CartProduct $cartProduct */
        $cartProduct = $em->getReference(CartProduct::class, $id);
        $cartProduct->setProduct(
            $em->getReference(Product::class, $product['id'])
        );
        $cartProduct->setType($type);
        $cartProduct->setQuantity($quantity);

        $em->flush();

        return $cartProduct;
    }

    public function deleteCartProduct($id)
    {
        $em = $this->getEntityManager();

        $cartProduct = $em->getReference(CartProduct::class, $id);

        $em->remove($cartProduct);
        $em->flush();
    }
}
