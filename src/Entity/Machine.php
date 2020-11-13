<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Machines
 *
 * @ORM\Table(name="machines", indexes={@ORM\Index(name="fk_machines_user", columns={"user_id"})})
 * @ORM\Entity
 */
class Machine
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", length=50, nullable=false)
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=150, nullable=false)
     */
    private $model;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturer", type="string", length=255, nullable=false)
     */
    private $manufacturer;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $price;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image_front_url", type="string", length=512, nullable=true)
     */
    private $imageFrontUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image_lateral_url", type="string", length=512, nullable=true)
     */
    private $imageLateralUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image_thumbnail_url", type="string", length=512, nullable=true)
     */
    private $imageThumbnailUrl;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $updatedAt = 'CURRENT_TIMESTAMP';

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImageFrontUrl(): ?string
    {
        return $this->imageFrontUrl;
    }

    public function setImageFrontUrl(?string $imageFrontUrl): self
    {
        $this->imageFrontUrl = $imageFrontUrl;

        return $this;
    }

    public function getImageLateralUrl(): ?string
    {
        return $this->imageLateralUrl;
    }

    public function setImageLateralUrl(?string $imageLateralUrl): self
    {
        $this->imageLateralUrl = $imageLateralUrl;

        return $this;
    }

    public function getImageThumbnailUrl(): ?string
    {
        return $this->imageThumbnailUrl;
    }

    public function setImageThumbnailUrl(?string $imageThumbnailUrl): self
    {
        $this->imageThumbnailUrl = $imageThumbnailUrl;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
