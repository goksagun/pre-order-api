<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cart_products")
 * @ORM\Entity(repositoryClass="App\Repository\CartProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CartProduct implements TimestampsInterface
{
    use TimestampsTrait;

    public const TYPE_CART_ITEM = 'cart_item';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Cart", inversedBy="items")
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     */
    private $product;

    /**
     * @ORM\Column(type="smallint")
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
