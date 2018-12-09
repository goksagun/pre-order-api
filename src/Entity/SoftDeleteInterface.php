<?php

namespace App\Entity;

interface SoftDeleteInterface
{
    public function getDeletedAt(): ?\DateTimeInterface;
    public function setDeletedAt(\DateTimeInterface $deletedAt);
}