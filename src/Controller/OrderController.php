<?php

namespace App\Controller;

use App\Entity\Order;
use App\Events;
use App\Repository\OrderRepository;
use App\Validation\OrderPostValidation;
use App\Validation\OrderPutValidation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/orders", methods={"GET"})
     */
    public function getOrders(OrderRepository $repository)
    {
        // You can also secure your controller using annotations after installing SensioFrameworkExtraBundle:
        // IsGranted("ROLE_ADMIN")
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->json(
            $repository->findOrders()
        );
    }

    /**
     * @Route("/carts/{cartId<\d+>}/checkout", methods={"POST"})
     */
    public function postOrder(Request $request, OrderPostValidation $validation, OrderRepository $repository)
    {
        // TODO: Create a controller listener and automate validations without calling run method in controller action.
        $validation->run($request);

        $order = $repository->insertOrder(
            $request->get('cartId'),
            $request->get('type'),
            $request->get('firstName'),
            $request->get('lastName'),
            $request->get('email'),
            $request->get('phone')
        );

        return $this->json(
            $this->transform($order),
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @Route("/orders/{id<\d+>}", methods={"GET"})
     */
    public function getOrder(Request $request)
    {
        return $this->json(
            [
                'id' => (int)$request->get('id'),
                'type' => 'order',
                'email' => 'customer@example.com',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ]
        );
    }

    /**
     * @Route("/orders/{id<\d+>}", methods={"PUT"})
     */
    public function putOrder(Request $request, OrderRepository $repository, OrderPutValidation $validation)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $validation->run($request);

        $order = $repository->updateOrder(
            $request->get('id'),
            $request->get('type'),
            $request->get('firstName'),
            $request->get('lastName'),
            $request->get('email'),
            $request->get('phone'),
            $request->get('status')
        );

        return $this->json(
            $this->transform($order)
        );
    }

    /**
     * @Route("/orders/{id<\d+>}", methods={"PATCH"})
     */
    public function patchOrderStatus(
        Request $request,
        OrderRepository $repository,
        EventDispatcherInterface $dispatcher
    ) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $order = $repository->updateOrderStatus(
            $request->get('id'),
            $request->get('status')
        );

        $event = new GenericEvent($order);

        $dispatcher->dispatch(Events::ORDER_CHANGED, $event);

        return $this->json(
            [
                [
                    'id' => $order->getId(),
                    'status' => $order->getStatus(),
                ],
            ]
        );
    }

    /**
     * @param Order $order
     * @return array
     */
    private function transform(Order $order): array
    {
        return [
            'id' => $order->getId(),
            'type' => $order->getType(),
            'firstName' => $order->getFirstName(),
            'lastName' => $order->getLastName(),
            'email' => $order->getEmail(),
            'phone' => $order->getPhone(),
            'status' => $order->getStatus(),
        ];
    }
}
