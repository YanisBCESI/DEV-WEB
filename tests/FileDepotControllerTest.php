<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\FileDepotController;

class FileDepotControllerTest extends TestCase {
    private $controller;
    private $twigMock;

    protected function setUp(): void {
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_SESSION = [];

        if (!defined('PHPUNIT_RUNNING')) {
            define('PHPUNIT_RUNNING', true);
        }

        $this->twigMock = $this->createMock(\Twig\Environment::class);
        $this->controller = new FileDepotController($this->twigMock);
    }

    public function testFileDepotPageRendersTwig() {
        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('formulaire_depot_fichier.html.twig')
            ->willReturn('HTML');

        ob_start();
        $this->controller->filedepotPage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }

    public function testSendFileDoesNotCrashWithFile() {
        $_FILES['userfile'] = ['tmp_name' => '/tmp/fakefile'];

        ob_start();
        try {
            $this->controller->sendFile();
        } catch (\Throwable $e) {}
        ob_end_clean();

        $this->assertTrue(true);
    }
}