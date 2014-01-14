<?php

namespace Veles\HomeWorkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\RawCommentInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="comments")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Comment extends BaseComment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Thread of this comment
     *
     * @var Thread
     * @ORM\ManyToOne(targetEntity="Veles\HomeWorkBundle\Entity\Thread")
     */
    protected $thread;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getThread() {
        return $this->thread;
    }

    public function setThread(\FOS\CommentBundle\Model\ThreadInterface $thread) {
        $this->thread = $thread;
        return $this;
    }
}