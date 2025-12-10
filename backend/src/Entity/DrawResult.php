<?php

namespace App\Entity;

use App\Traits\Utils;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;

#[ORM\Entity]
#[ORM\Table(name: 'draw_results')]
class DrawResult
{
    use Utils;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id {
        get {
            return $this->id;
        }
    }

    #[ORM\OneToOne(targetEntity: Draw::class, inversedBy: 'result')]
    #[ORM\JoinColumn(name: 'draw_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Draw $draw {
        get {
            return $this->draw;
        }
        set(Draw $value) {
            $this->draw = $value;
        }
    }

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $payload = null {
        get {
            return $this->payload;
        }
        set {
            $this->payload = $value;
        }
    }

    /**
     * @throws RandomException
     */
    public function __construct(Draw $draw, ?array $payload = null)
    {
        $this->id = self::generateUuidV4();
        $this->draw = $draw;
        $this->payload = $payload;
    }
    public function getPayload(): ?array
    {
        return $this->payload;
    }

}
