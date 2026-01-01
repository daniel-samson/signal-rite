<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository for ProcedureCode entities.
 *
 * Provides methods for querying CPT/HCPCS procedure codes.
 */
class ProcedureCodeRepository extends EntityRepository
{
    /**
     * Find a procedure code by its code string.
     *
     * @param string $code The CPT/HCPCS code (with or without modifier)
     *
     * @return \AppBundle\Entity\ProcedureCode|null
     */
    public function findByCode(string $code)
    {
        return $this->findOneBy(['code' => strtoupper(trim($code))]);
    }

    /**
     * Find procedure codes by category.
     *
     * @param string $category The procedure category
     *
     * @return \AppBundle\Entity\ProcedureCode[]
     */
    public function findByCategory(string $category): array
    {
        return $this->findBy(['category' => $category], ['code' => 'ASC']);
    }

    /**
     * Search procedure codes by code prefix.
     *
     * @param string $prefix The code prefix to search for
     *
     * @return \AppBundle\Entity\ProcedureCode[]
     */
    public function findByCodePrefix(string $prefix): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.code LIKE :prefix')
            ->setParameter('prefix', strtoupper(trim($prefix)) . '%')
            ->orderBy('p.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all codes with a specific modifier.
     *
     * @param string $modifier The modifier to search for (e.g., "25", "TC")
     *
     * @return \AppBundle\Entity\ProcedureCode[]
     */
    public function findByModifier(string $modifier): array
    {
        $modifier = strtoupper(trim($modifier));

        return $this->createQueryBuilder('p')
            ->where('p.code LIKE :pattern')
            ->setParameter('pattern', '%-' . $modifier)
            ->orderBy('p.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find the base code (without modifier) for a given code.
     *
     * @param string $code The full code (e.g., "99213-25")
     *
     * @return \AppBundle\Entity\ProcedureCode|null The base code entity (e.g., "99213")
     */
    public function findBaseCode(string $code)
    {
        $parts = explode('-', strtoupper(trim($code)));
        return $this->findByCode($parts[0]);
    }
}
