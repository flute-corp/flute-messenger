<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Serializer\Groups({"getMessage"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     *
     * @Serializer\Groups({"getMessage", "postMessage"})
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Groups({"getMessage"})
     */
    private $dateEtHeure;

    /**
     * @var Conversation
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Conversation")
     */
    private $conversation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDateEtHeure(): ?\DateTimeInterface
    {
        return $this->dateEtHeure;
    }

    public function setDateEtHeure(\DateTimeInterface $dateEtHeure): self
    {
        $this->dateEtHeure = $dateEtHeure;

        return $this;
    }

    /**
     * @param Conversation $conversation
     * @return Message
     */
    public function setConversation(Conversation $conversation): Message
    {
        $this->conversation = $conversation;
        return $this;
    }

    /**
     * @return Conversation
     */
    public function getConversation(): Conversation
    {
        return $this->conversation;
    }
}
