<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidTest extends AbstractProgrammesServiceTest
{
    public function testFindByPid()
    {
        $pid = new Pid('b010t19z');
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findByPid')
            ->with($pid)
            ->willReturn($dbData);

        $result = $this->service()->findByPid($pid);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindByPidEmptyData()
    {
        $pid = new Pid('b010t19z');

        $this->mockRepository->expects($this->once())
            ->method('findByPid')
            ->with($pid)
            ->willReturn(null);

        $result = $this->service()->findByPid($pid);
        $this->assertNull($result);
    }
}
