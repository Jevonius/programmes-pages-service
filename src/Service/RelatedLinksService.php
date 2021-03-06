<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\RelatedLinkRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\RelatedLinkMapper;

class RelatedLinksService extends AbstractService
{
    /* @var RelatedLinkMapper */
    protected $mapper;

    /* @var RelatedLinkRepository */
    protected $repository;

    public function __construct(
        RelatedLinkRepository $repository,
        RelatedLinkMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByRelatedToProgramme(
        Programme $programme,
        array $linkTypes = [],
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), implode('|', $linkTypes), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $linkTypes, $limit, $page) {
                $dbEntities = $this->repository->findByRelatedTo(
                    [$programme->getDbId()],
                    'programme',
                    $linkTypes,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }
}
