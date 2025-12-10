<?php

namespace App\Entity;

use App\Traits\Utils;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;

#[ORM\Entity]
#[ORM\Table(name: 'draw_options')]
class DrawOption
{
    use Utils;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $id {
        get {
            return $this->id;
        }
    }

    #[ORM\ManyToOne(targetEntity: Draw::class, inversedBy: 'options')]
    #[ORM\JoinColumn(name: 'draw_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    public ?Draw $draw = null {
        get {
            return $this->draw;
        }
        set(?Draw $value) {
            $this->draw = $value;
        }
    }

    #[ORM\Column(type: 'string', length: 255)]
    public string $content {
        get {
            return $this->content;
        }
        set {
            $this->content = $value;
        }
    }

    #[ORM\Column(type: 'string', length: 255)]
    public string $author {
        get {
            return $this->author;
        }
        set {
            $this->author = $value;
        }
    }

    /**
     * @throws RandomException
     */
    public function __construct(string $content, string $author = 'Anonymous')
    {
        $this->id = self::generateUuidV4();
        $this->content = $content;
        $this->author = $author;
    }
}
