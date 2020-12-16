<?php

// https://medium.com/@ger86/symfony-introducci%C3%B3n-al-componente-messenger-i-e2a4df1adc40

namespace App\Message;

class NotificationMessage
{
    private string $message;

    private array $users;

    public function __construct(string $message = '', array $users = [])
    {
        $this->message = $message;
        $this->users = $users;
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
     * @return NotificationMessage
     */
    public function setMessage(string $message): NotificationMessage
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param array $users
     * @return NotificationMessage
     */
    public function setUsers(array $users): NotificationMessage
    {
        $this->users = $users;
        return $this;
    }
}
