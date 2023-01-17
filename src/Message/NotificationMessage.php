<?php

// https://medium.com/@ger86/symfony-introducci%C3%B3n-al-componente-messenger-i-e2a4df1adc40

namespace App\Message;

class NotificationMessage
{
    public function __construct(
        protected string $message = '',
        protected array $users = []
    ) {
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param array<string> $users
     * @return void
     */
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }
}
