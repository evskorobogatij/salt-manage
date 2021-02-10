<?php

namespace App\Entity\Helpers;

use App\Entity\Minion;
use App\Repository\Helpers\OsFullNameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Table(name="""helpers"".""os_full_name""")
 * @ORM\Entity(repositoryClass=OsFullNameRepository::class)
 */
class OsFullName
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Minion::class, mappedBy="os_full_name")
     */
    private $minions;

    public function __construct()
    {
        $this->minions = new ArrayCollection();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection|Minion[]
     */
    public function getMinions(): Collection
    {
        return $this->minions;
    }

    public function addMinion(Minion $minion): self
    {
        if (!$this->minions->contains($minion)) {
            $this->minions[] = $minion;
            $minion->setOsFullName($this);
        }

        return $this;
    }

    public function removeMinion(Minion $minion): self
    {
        if ($this->minions->removeElement($minion)) {
            // set the owning side to null (unless already changed)
            if ($minion->getOsFullName() === $this) {
                $minion->setOsFullName(null);
            }
        }

        return $this;
    }
}
