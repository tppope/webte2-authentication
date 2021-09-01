<?php


class User implements \JsonSerializable
{
    private int $id;
    private string $name;
    private string $surname;
    private string $email;
    private string $loginType;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getLoginType(): string
    {
        return $this->loginType;
    }

    /**
     * @param string $loginType
     */
    public function setLoginType(string $loginType): void
    {
        $this->loginType = $loginType;
    }


    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
