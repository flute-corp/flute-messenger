<?php

namespace App\Entity;

use App\Traits\BlameableUserCreator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConversationRepository")
 */
class Conversation
{
    use BlameableUserCreator;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Serializer\Groups({"getConversation"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Groups({"getConversation", "postConversation", "putConversation"})
     * @Serializer\Accessor(getter="getLibelle")
     */
    private $libelle;

    /**
     * @var ArrayCollection
     *
     * @Serializer\Groups({"postConversation", "putConversation"})
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="conversations")
     */
    private $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        if ($this->libelle) return $this->libelle;

        $aPart = $this->getAllParticipants();
        if (!$aPart) return 'Aucun participants';
        return implode(', ', array_map(function (User $oUser) { return $oUser->getUsername(); }, $aPart));
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): ?Collection
    {
        return $this->participants;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("participants")
     * @Serializer\Groups({"getConversation"})
     * @return Collection|null
     */
    public function getAllParticipants(): ?array
    {
        $aPart = $this->getParticipants()->toArray();
        array_push($aPart, $this->getCreatedBy());
        return $aPart;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
        }

        return $this;
    }

}
