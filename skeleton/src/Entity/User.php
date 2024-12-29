<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(length: 255)]
    private ?string $patronumic = null;

    /**
     * @var Collection<int, Wall>
     */
    #[ORM\OneToMany(targetEntity: Wall::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $walls;

    public function __construct()
    {
        $this->walls = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPatronumic(): ?string
    {
        return $this->patronumic;
    }

    public function setPatronumic(string $patronumic): static
    {
        $this->patronumic = $patronumic;

        return $this;
    }

    /**
     * @return Collection<int, Wall>
     */
    public function getWalls(): Collection
    {
        return $this->walls;
    }

    public function addWall(Wall $wall): static
    {
        if (!$this->walls->contains($wall)) {
            $this->walls->add($wall);
            $wall->setAuthor($this);
        }

        return $this;
    }

    public function removeWall(Wall $wall): static
    {
        if ($this->walls->removeElement($wall)) {
            // set the owning side to null (unless already changed)
            if ($wall->getAuthor() === $this) {
                $wall->setAuthor(null);
            }
        }

        return $this;
    }
}
