<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ServiceRepository extends EntityRepository
{
    public function findBySids(array $serviceIds): array
    {
        $qb = $this->createQueryBuilder('service')
            ->addSelect(['network'])
            ->leftJoin('service.network', 'network')
            ->where('service.sid IN (:dbIds)')
            ->setParameter('dbIds', $serviceIds);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findByIds(array $ids): array
    {
        return $this->createQueryBuilder('service')
            ->addSelect(['masterBrand', 'network'])
            ->leftJoin('service.masterBrand', 'masterBrand')
            ->leftJoin('service.network', 'network')
            ->where("service.id IN(:ids)")
            ->setParameter('ids', $ids)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
