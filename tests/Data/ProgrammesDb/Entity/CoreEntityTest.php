<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand;
use PHPUnit_Framework_TestCase;

class CoreEntityTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity'
        );

        $this->assertSame(null, $entity->getId());
        $this->assertSame(null, $entity->getPid());
        $this->assertSame(false, $entity->getIsEmbargoed());
        $this->assertSame('', $entity->getTitle());
        $this->assertSame('', $entity->getSearchTitle());
        $this->assertSame(null, $entity->getParent());
        $this->assertSame('', $entity->getAncestry());
        $this->assertSame('', $entity->getShortSynopsis());
        $this->assertSame('', $entity->getLongSynopsis());
        $this->assertSame('', $entity->getMediumSynopsis());
        $this->assertSame(null, $entity->getImage());
        $this->assertSame(null, $entity->getMasterBrand());
        $this->assertSame(0, $entity->getRelatedLinksCount());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity'
        );

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'a-string'],
            ['IsEmbargoed', true],
            ['Title', 'a-string'],
            ['SearchTitle', 'a-string'],
            ['Parent', new Brand()],
            //ancestry
            ['ShortSynopsis', 'a-string'],
            ['MediumSynopsis', 'a-string'],
            ['LongSynopsis', 'a-string'],
            ['Image', new Image()],
            ['MasterBrand', new MasterBrand('mid', 'name')],
            ['RelatedLinksCount', 1],
        ];
    }
}
