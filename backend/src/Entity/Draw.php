<?php

namespace App\Entity;

use App\Traits\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;

#[ORM\Entity]
#[ORM\Table(name: 'draws')]
class Draw
{
    use Utils;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $id {
        get {
            return $this->id;
        }
    }

    #[ORM\Column(type: 'string', length: 255)]
    public string $name {
        get {
            return $this->name;
        }
        set {
            $this->name = $value;
        }
    }

    #[ORM\Column(type: 'integer')]
    public int $resultsCount {
        get {
            return $this->resultsCount;
        }
        set {
            $this->resultsCount = $value;
        }
    }

    /** @var Collection<int, DrawOption> */
    #[ORM\OneToMany(targetEntity: DrawOption::class, mappedBy: 'draw', cascade: ['persist', 'remove'], orphanRemoval: true)]
    public Collection $options {
        get {
            return $this->options;
        }
    }

    #[ORM\Column(enumType: DrawStatus::class)]
    public DrawStatus $status {
        get {
            return $this->status;
        }
        set(DrawStatus $value) {
            $this->status = $value;
        }
    }

    #[ORM\OneToOne(targetEntity: DrawResult::class, mappedBy: 'draw', cascade: ['persist', 'remove'])]
    private ?DrawResult $result = null {
        get {
            return $this->result;
        }
        set(?DrawResult $value) {
            $this->result = $value;
        }
    }

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt {
        get {
            return $this->createdAt;
        }
    }

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $finishedAt = null {
        get {
            return $this->finishedAt;
        }
        set {
            $this->finishedAt = $value;
        }
    }

    /**
     * @throws RandomException
     */
    public function __construct(string $name, int $resultsCount)
    {
        $this->id = self::generateUuidV4();
        $this->name = $name;
        $this->resultsCount = $resultsCount;
        $this->options = new ArrayCollection();
        $this->status = DrawStatus::IN_PROGRESS;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function addOption(DrawOption $option): void
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->draw = $this;
        }
    }

    public function setResult(DrawResult $result): void
    {
        $this->result = $result;
    }

    public function getResult(): ?DrawResult
    {
        return $this->result;
    }
}
