<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\AdminController;

class AdminControllerTest extends TestCase {

    private $controller;

    protected function setUp(): void {
        $twigMock = $this->createMock(\Twig\Environment::class);
        $this->controller = new AdminController($twigMock);
    }
    public function testGetPilotFormMessageSuccess() {
        $method = new ReflectionMethod($this->controller, 'getPilotFormMessage');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, 'created');

        $this->assertEquals('success', $result['type']);
        $this->assertEquals('Le compte pilote a ete cree avec succes.', $result['text']);
    }

    public function testGetPilotFormMessageError() {
        $method = new ReflectionMethod($this->controller, 'getPilotFormMessage');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, 'email_exists');

        $this->assertEquals('error', $result['type']);
    }

    public function testGetPilotFormMessageDefault() {
        $method = new ReflectionMethod($this->controller, 'getPilotFormMessage');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, 'unknown');

        $this->assertNull($result);
    }
    public function testRequireLoggedAdminReturnsAdmin() {
        $_SESSION["admin"] = [
            "id" => 1,
            "nom" => "Test"
        ];

        $method = new ReflectionMethod($this->controller, 'requireLoggedAdmin');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller);

        $this->assertEquals(1, $result["id"]);
    }
}