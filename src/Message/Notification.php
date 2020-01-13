<?php

// https://medium.com/@ger86/symfony-introducci%C3%B3n-al-componente-messenger-i-e2a4df1adc40

namespace App\Message;


class Notification
{
    /** @var string $message */
    private $message;

    /** @var array $users */
    private $users;

    public function __construct(string $message, array $users)
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
     * @return Notification
     */
    public function setMessage(string $message): Notification
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
     * @return Notification
     */
    public function setUsers(array $users): Notification
    {
        $this->users = $users;
        return $this;
    }
}