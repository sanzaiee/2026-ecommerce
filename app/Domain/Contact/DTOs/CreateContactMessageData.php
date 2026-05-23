<?php

namespace App\Domain\Contact\DTOs;

readonly class CreateContactMessageData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $subject,
        public string $message,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
        ];
    }
}
