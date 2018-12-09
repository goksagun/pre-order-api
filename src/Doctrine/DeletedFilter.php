<?php

namespace App\Doctrine;

use App\Entity\SoftDeleteInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DeletedFilter extends SQLFilter
{

    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetaData $targetEntity
     * @param string $targetTableAlias
     *
     * @return string The constraint SQL if there is available, empty string otherwise.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->getReflectionClass()->implementsInterface(SoftDeleteInterface::class)) {
            return '';
        }

        return sprintf(
            '%s.deleted_at >= \'%s\' OR %s.deleted_at IS NULL',
            $targetTableAlias,
            date('Y-m-d H:i:s'),
            $targetTableAlias
        );
    }
}