<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/carts", methods={"GET"})
     */
    public function getCarts(CartRepository $repository)
    {
        return $this->json(
            $repository->findCarts()
        );
    }

    /**
     * @Route("/carts", methods={"POST"})
     */
    public function postCart(Request $request, CartRepository $repository)
    {
        $cart = $repository->insertCart(
            $request->get('type')
        );

        // TODO: use object normalizer
        return $this->transformCart($cart, Response::HTTP_CREATED);
    }

    /**
     * @Route("/carts/{id<\d+>}", methods={"GET"})
     */
    public function getCart(Request $request, CartRepository $repository)
    {
        $cart = $repository->findCart(
            $request->get('id')
        );

        return $this->json($cart);
    }

    /**
     * @Route("/carts/{id<\d+>}", methods={"PUT"})
     */
    public function putCart(Request $request, CartRepository $repository)
    {
        $cart = $repository->updateCart(
            $request->get('id'),
            $request->get('type')
        );

        // TODO: use object normalizer
        return $this->transformCart($cart);
    }

    /**
     * @Route("/carts/{id<\d+>}", methods={"DELETE"})
     */
    public function deleteCart(Request $request, CartRepository $repository)
    {
        $repository->deleteCart($request->get('id'));

        return $this->json([], JsonResponse::HTTP_NO_CONTENT);
    }

    private function transformCart(Cart $cart, $status = Response::HTTP_OK): JsonResponse
    {
        return $this->json(
            [
                'id' => $cart->getId(),
                'type' => $cart->getType(),
            ],
            $status
        );
    }
}
