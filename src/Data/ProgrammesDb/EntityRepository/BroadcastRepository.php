<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;

class BroadcastRepository extends EntityRepository
{
    use Traits\ParentTreeWalkerTrait;

    public function findByVersion(array $dbIds, string $type, ?int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('broadcast')
            ->addSelect(['service', 'network'])
            // Left join as Webcasts are not attached to services
            ->leftJoin('broadcast.service', 'service')
            ->leftJoin('service.network', 'network')
            ->andWhere("broadcast.version IN (:dbIds)")
            ->addOrderBy('broadcast.startAt', 'DESC')
            ->addOrderBy('service.sid', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('dbIds', $dbIds);

        $this->setEntityTypeFilter($qb, $type);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findEmbargoedBroadcastsAfter(DateTimeImmutable $from)
    {
        $qb = $this->createQueryBuilder('broadcast')
            ->addSelect(['episode', 'service'])
            ->innerJoin('broadcast.programmeItem', 'episode')
            ->innerJoin('broadcast.service', 'service')
            ->andWhere('episode.isEmbargoed = true')
            ->andWhere('broadcast.endAt > :from')
            // earliest first
            ->addOrderBy('broadcast.endAt', 'ASC')
            ->setParameter('from', $from);

        $this->setEntityTypeFilter($qb, 'Broadcast');

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function createQueryBuilder($alias, $joinViaVersion = true, $indexBy = null)
    {
        // Any time Broadcasts are fetched here they must be inner joined to
        // their programme entity - either directly - or via the version, this
        // allows the embargoed filter to trigger and exclude unwanted items.
        // This ensures that Broadcasts that belong to a version that belongs
        // to an embargoed programme are never returned
        if ($joinViaVersion) {
            return parent::createQueryBuilder($alias)
                ->join($alias . '.version', 'version')
                ->join('version.programmeItem', 'programmeItem');
        }

        return parent::createQueryBuilder($alias)
            ->join($alias . '.programmeItem', 'programmeItem');
    }

    public function findAllByServiceAndDateRange(
        Sid $serviceId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit,
        int $offset
    ):array {
        $qb = $this->createQueryBuilder('broadcast', false)
            ->addSelect(['programmeItem', 'service', 'masterBrand', 'network', 'serviceNetwork'])
            ->innerJoin('programmeItem.masterBrand', 'masterBrand')
            ->innerJoin('masterBrand.network', 'network')
            ->innerJoin('broadcast.service', 'service')
            ->innerJoin('service.network', 'serviceNetwork')

            ->andWhere('broadcast.startAt >= :startDate')
            ->andWhere('broadcast.startAt < :endDate')
            ->andWhere('service.sid = :sid')
            ->addOrderBy('broadcast.startAt', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)

            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('sid', $serviceId);

        $this->setEntityTypeFilter($qb, 'Broadcast');

        $results = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $broadcasts = $this->abstractResolveAncestry(
            $results,
            [$this, 'programmeAncestryGetter'],
            ['programmeItem', 'ancestry']
        );

        return $broadcasts;
    }

    private function entityTypeFilterValue(string $type): ?bool
    {
        $typesLookup = [
            'Broadcast' => false,
            'Webcast' => true,
            'Any' => null,
        ];

        $typeNames = array_keys($typesLookup);

        if (!in_array($type, $typeNames)) {
            throw new InvalidArgumentException(sprintf(
                'Called %s with an invalid type. Expected one of %s but got "%s"',
                debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'],
                '"' . implode('", "', $typeNames) . '"',
                $type
            ));
        }

        return $typesLookup[$type] ?? null;
    }

    private function setEntityTypeFilter(QueryBuilder $qb, string $type, string $broadcastAlias = 'broadcast'): QueryBuilder
    {
        $isWebcast = $this->entityTypeFilterValue($type);

        if (!is_null($isWebcast)) {
            $qb->andWhere($broadcastAlias . '.isWebcast = :isWebcast')
                ->setParameter('isWebcast', $isWebcast);
        }

        return $qb;
    }

    private function programmeAncestryGetter(array $ids): array
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $repo->findByIds($ids);
    }
}
