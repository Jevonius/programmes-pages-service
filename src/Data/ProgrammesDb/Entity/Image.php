<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Image
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $shortSynopsis = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $type = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $extension = 'jpg';


    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getPid()
    {
        return $this->pid;
    }

    public function setPid(string $pid)
    {
        $this->pid = $pid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function setShortSynopsis(string $shortSynopsis)
    {
        $this->shortSynopsis = $shortSynopsis;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension)
    {
        $this->extension = $extension;
    }
}
