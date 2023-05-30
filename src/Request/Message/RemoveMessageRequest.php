<?php

namespace App\Request\Message;

use App\Validator\CsrfToken;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RemoveMessageRequest {

    #[CsrfToken(id: 'remove_message')]
    #[Assert\NotBlank]
    #[Serializer\SerializedName('_token')]
    #[Serializer\Type('string')]
    private ?string $csrfToken = null;

    public function getCsrfToken(): ?string {
        return $this->csrfToken;
    }

    public function setCsrfToken(?string $csrfToken): RemoveMessageRequest {
        $this->csrfToken = $csrfToken;
        return $this;
    }
}