<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OrderBy;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TreeRepository")
 */
class Tree {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * One Category has Many Categories.
     * @OneToMany(targetEntity="Tree", mappedBy="parent")
     * @OrderBy({"sequence" = "ASC"})
     */
    private $children;

    /**
     * Many Categories have One Category.
     * @ManyToOne(targetEntity="Tree", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;


    /**
     * @ORM\Column(type="integer")
     */
    private $depth;

    /**
     * @ORM\Column(type="integer")
     */
    private $sequence;

    public function __construct() {
        $this->children = new ArrayCollection();
        $this->sequence = 0;
    }

    /**
     * @return mixed
     */
    public function getDepth() {
        return $this->depth;
    }

    /**
     * @param mixed $depth
     */
    public function setDepth($depth): void {
        $this->depth = $depth;
    }

    /**
     * @return mixed
     */
    public function getSequence() {
        return $this->sequence;
    }

    /**
     * @param mixed $sequence
     */
    public function setSequence($sequence): void {
        $this->sequence = $sequence;
    }



    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children): void {
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent): void {
        $this->parent = $parent;
    }



}
