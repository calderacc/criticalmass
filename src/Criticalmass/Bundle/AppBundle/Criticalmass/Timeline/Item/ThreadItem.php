<?php

namespace Criticalmass\Bundle\AppBundle\Criticalmass\Timeline\Item;

use Criticalmass\Bundle\AppBundle\Entity\Thread;

class ThreadItem extends AbstractItem
{
    /** @var string $username */
    public $username;

    /** @var Thread $thread */
    public $thread;

    /** @var string $title */
    public $title;

    /** @var string $text */
    public $text;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): ThreadItem
    {
        $this->username = $username;

        return $this;
    }

    public function getThread(): Thread
    {
        return $this->thread;
    }

    public function setThread(Thread $thread): ThreadItem
    {
        $this->thread = $thread;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): ThreadItem
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): ThreadItem
    {
        $this->text = $text;

        return $this;
    }

}
