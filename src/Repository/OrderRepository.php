<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findOrders($hydrationMode = Query::HYDRATE_ARRAY)
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult($hydrationMode)
        ;
    }

    public function insertOrder($cartId, $type, $firstName, $lastName, $email, $phone)
    {
        $em = $this->getEntityManager();

        /** @var Cart $cart */
        $cart = $em->getReference(Cart::class, $cartId);

        $order = new Order();
        $order->setCart($cart);
        $order->setType($type);
        $order->setFirstName($firstName);
        $order->setLastName($lastName);
        $order->setEmail($email);
        $order->setPhone($phone);

        foreach ($cart->getItems() as $cartProduct) {
            $orderProduct = new OrderProduct();
            $orderProduct->setSku($cartProduct->getProduct()->getSku());
            $orderProduct->setName($cartProduct->getProduct()->getName());
            $orderProduct->setStock($cartProduct->getProduct()->getStock());
            $orderProduct->setPrice($cartProduct->getProduct()->getPrice());
            $orderProduct->setQuantity($cartProduct->getQuantity());

            $order->addItem($orderProduct);
        }

        $em->persist($order);
        $em->flush();

        return $order;
    }

    public function updateOrder($id, $type, $firstName, $lastName, $email, $phone, $status)
    {
        $em = $this->getEntityManager();

        /** @var Order $order */
        $order = $em->getReference(Order::class, $id);

        $order->setType($type);
        $order->setFirstName($firstName);
        $order->setLastName($lastName);
        $order->setEmail($email);
        $order->setPhone($phone);
        $order->setStatus($status);

        $em->flush();

        return $order;
    }

    public function updateOrderStatus($id, $status)
    {
        $em = $this->getEntityManager();

        /** @var Order $order */
        $order = $em->getReference(Order::class, $id);

        $order->setStatus($status);

        $em->flush();

        return $order;
    }
}
