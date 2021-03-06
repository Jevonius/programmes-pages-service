<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(indexes={
 *      @ORM\Index(name="category_ancestry_idx", columns={"ancestry"}),
 *      @ORM\Index(name="category_type_idx", columns={"type"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository")
 * @Gedmo\Tree(type="materializedPath", cascadeDeletes=false)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\MappedSuperclass()
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *   "genre"="Genre",
 *   "format"="Format",
 * })
 */
abstract class Category
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Gedmo\TreePathSource()
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\TreePath()
     */
    private $ancestry;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    private $children;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $urlKey;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false, unique=true)
     */
    private $pipId;

    /**
     * @var int
     *
     * @Gedmo\TreeLevel()
     * @ORM\Column(type="integer", nullable=false, options={"default" = 1})
     */
    protected $depth = 1;

    //// Denormalisations

    /**
     * A list of the programmes that have this category.
     *
     * @ORM\ManyToMany(targetEntity="CoreEntity", mappedBy="categories")
     */
    protected $programmes;

    /**
     * Category constructor.
     *
     * @param string $pipId
     * @param string $title
     * @param string $urlKey
     */
    public function __construct(string $pipId, string $title, string $urlKey)
    {
        $this->pipId = $pipId;
        $this->title = $title;
        $this->urlKey = $urlKey;
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAncestry(): ?string
    {
        return $this->ancestry;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getPipId(): string
    {
        return $this->pipId;
    }

    public function setPipId(string $pipId): void
    {
        $this->pipId = $pipId;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent)
    {
        $this->parent = $parent;
    }

    public function getUrlKey(): string
    {
        return $this->urlKey;
    }

    public function setUrlKey(string $urlKey): void
    {
        $this->urlKey = $urlKey;
    }

    public function getChildren(): DoctrineCollection
    {
        return $this->children;
    }
}
