<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="carts")
 * @ORM\Entity(repositoryClass="App\Repository\CartRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Cart implements TimestampsInterface, SoftDeleteInterface
{
    use TimestampsTrait, SoftDeleteTrait;

    public const TYPE_CART = 'cart';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(
     *     type="string",
     *     length=255,
     *     options={"comment"="This represents the type of object being returned", "default"="cart"}
     * )
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="CartProduct", mappedBy="cart", cascade={"persist", "remove"})
     */
    private $items;

    /**
     * Cart constructor.
     */
    public function __construct()
    {
        $this->type = self::TYPE_CART;
        $this->items = new ArrayCollection();
    }

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

    /**
     * @return Collection|CartProduct[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(CartProduct $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setCart($this);
        }

        return $this;
    }

    public function removeItem(CartProduct $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getCart() === $this) {
                $item->setCart(null);
            }
        }

        return $this;
    }
}
