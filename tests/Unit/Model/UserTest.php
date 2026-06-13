<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testNom(): void
    {
        $this->user->setNom("Ismail");
        $this->assertEquals("Ismail", $this->user->getNom());
    }

    public function testEmail(): void
    {
        $this->user->setEmail("ismail.zamouri3@gmail.com");
        $this->assertEquals("ismail.zamouri3@gmail.com", $this->user->getEmail());
    }
}