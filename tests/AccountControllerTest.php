<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\AccountController;

class AccountControllerTest extends TestCase {

    private $controller;

    protected function setUp(): void {
        $twigMock = $this->createMock(\Twig\Environment::class);
        $this->controller = new AccountController($twigMock);
    }

    public function testGetData() {
        $result = $this->controller->getData();
        $this->assertNotNull($result);
    }
    public function testGetUploadMessageSuccess() {
        $method = new ReflectionMethod($this->controller, 'getUploadMessage');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, 'success');

        $this->assertEquals("Document depose avec succes.", $result);
    }

    public function testGetUploadMessageInvalidType() {
        $method = new ReflectionMethod($this->controller, 'getUploadMessage');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, 'invalid_type');

        $this->assertEquals("Format non autorise : seuls les fichiers PDF et DOCX sont acceptes.", $result);
    }

    public function testGetUploadMessageDefault() {
        $method = new ReflectionMethod($this->controller, 'getUploadMessage');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, 'unknown');

        $this->assertNull($result);
    }
    public function testFormatStudentDocumentLabel() {
        $method = new ReflectionMethod($this->controller, 'formatStudentDocumentLabel');
        $method->setAccessible(true);

        $result = $method->invoke(
            $this->controller,
            12,
            "student_12_123456_cv_stage.pdf"
        );

        $this->assertEquals("cv stage.pdf", $result);
    }
    public function testGetDataReturnsArray() {
        $mockModel = $this->createMock(\App\Models\AccountModel::class);

        $mockModel->method('getData')
                ->willReturn(['test' => 'ok']);

        $twigMock = $this->createMock(\Twig\Environment::class);

        $controller = new AccountController($twigMock);

        // injection du mock
        $reflection = new ReflectionProperty($controller, 'account_model');
        $reflection->setAccessible(true);
        $reflection->setValue($controller, $mockModel);

        $result = $controller->getData();

        $this->assertEquals(['test' => 'ok'], $result);
    }
    public function testRequireLoggedStudentReturnsId() {
        $_SESSION["student"]["id"] = 42;

        $method = new ReflectionMethod($this->controller, 'requireLoggedStudent');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller);

        $this->assertEquals(42, $result);
    }
}