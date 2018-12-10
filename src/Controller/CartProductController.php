<?php

namespace App\Controller;

use App\Repository\CartProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartProductController extends AbstractController
{
    /**
     * @Route("/carts/{id<\d+>}/items", methods={"GET"})
     */
    public function getCartProducts(Request $request, CartProductRepository $repository)
    {
        $cartProducts = $repository->findByCartId(
            $request->get('id')
        );

        return $this->json($cartProducts);
    }

    /**
     * @Route("/carts/{id<\d+>}/items", methods={"POST"})
     */
    public function postCartProduct(Request $request, CartProductRepository $repository)
    {
        $cartProduct = $repository->insertCartProduct(
            $request->get('id'),
            $request->get('type'),
            $request->get('quantity'),
            $request->get('product')
        );

        return $this->json(
            [
                'id' => $cartProduct->getId(),
                'type' => $cartProduct->getType(),
                'quantity' => $cartProduct->getQuantity(),
                'product' => [
                    'id' => $cartProduct->getProduct()->getId(),
                ],
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @Route("/carts/{cartId<\d+>}/items/{id<\d+>}", methods={"PUT"})
     */
    public function putCartProduct(Request $request, CartProductRepository $repository)
    {
        $cartProduct = $repository->updateCartProduct(
            $request->get('id'),
            $request->get('type'),
            $request->get('quantity'),
            $request->get('product')
        );

        return $this->json(
            [
                'id' => $cartProduct->getId(),
                'type' => $cartProduct->getType(),
                'quantity' => $cartProduct->getQuantity(),
                'product' => [
                    'id' => $cartProduct->getProduct()->getId(),
                ],
            ]
        );
    }

    /**
     * @Route("/carts/{cartId<\d+>}/items/{id<\d+>}", methods={"DELETE"})
     */
    public function deleteCartProduct(Request $request, CartProductRepository $repository)
    {
        $repository->deleteCartProduct(
            $request->get('id')
        );

        return $this->json([], JsonResponse::HTTP_NO_CONTENT);
    }
}
