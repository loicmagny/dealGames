<?php

namespace App\Entity;

use App\Repository\AdvertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: AdvertRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Advert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $UpdatedAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    private $reference;

    #[ORM\OneToMany(mappedBy: 'advert', targetEntity: Categories::class)]
    private $category;

    #[ORM\OneToMany(mappedBy: 'advert', targetEntity: Images::class, orphanRemoval:true, cascade:["persist"])]
    private $images;

    /**
     * @Vich\UploadableField(mapping="advert_image", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    #[ORM\ManyToMany(targetEntity: user::class, inversedBy: 'favorite')]
    private $favorite;

    #[ORM\Column(type: 'boolean')]
    private $isActive;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'adverts')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->favorite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $UpdatedAt): self
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return Collection<int, Categories>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    // public function addCategory(Categories $category): self
    // {
    //     if (!$this->category->contains($category)) {
    //         $this->category[] = $category;
    //         $category->setAdvert($this);
    //     }

    //     return $this;
    // }

    // public function removeCategory(Categories $category): self
    // {
    //     if ($this->category->removeElement($category)) {
    //         // set the owning side to null (unless already changed)
    //         if ($category->getAdvert() === $this) {
    //             $category->setAdvert(null);
    //         }
    //     }

    //     return $this;
    // }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setTimestamp()
    {
        if (is_null($this->getCreatedAt())) {
            $this->setCreatedAt(new \DateTimeImmutable());
            $this->setUpdatedAt(new \DateTimeImmutable());
        } else {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImages(Images $images): self
    {
        if (!$this->images->contains($images)) {
            $this->images[] = $images;
            $images->setAdvert($this);
        }

        return $this;
    }

    public function removeImages(Images $image): self
    {
        if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAdvert() === $this) {
                $image->setAdvert(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getFavorite(): Collection
    {
        return $this->favorite;
    }

    public function addFavorite(user $favorite): self
    {
        if (!$this->favorite->contains($favorite)) {
            $this->favorite[] = $favorite;
        }

        return $this;
    }

    public function removeFavorite(user $favorite): self
    {
        $this->favorite->removeElement($favorite);

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
