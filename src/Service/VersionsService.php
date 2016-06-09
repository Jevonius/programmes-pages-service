<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper;

class VersionsService extends AbstractService
{
    public function __construct(
        VersionRepository $repository,
        VersionMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findByProgrammeItemDbId(int $dbid): array
    {
        $dbEntities = $this->repository->findByProgrammeItem($dbid);
        return $this->mapManyEntities($dbEntities);
    }
}