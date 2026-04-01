<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\LegalController;

class LegalControllerTest extends TestCase {
    private $controller;
    private $twigMock;

    protected function setUp(): void {
        $this->twigMock = $this->createMock(\Twig\Environment::class);
        $this->controller = new LegalController($this->twigMock);
    }

    public function testLegalNoticePageRendersTwig() {
        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('mentions_legales.html.twig')
            ->willReturn('HTML');

        ob_start();
        $this->controller->legalNoticePage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }

    public function testCookiesPageRendersTwig() {
        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('cookies.html.twig')
            ->willReturn('HTML');

        ob_start();
        $this->controller->cookiesPage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }

    public function testHelpPageRendersTwig() {
        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('besoin_aide.html.twig')
            ->willReturn('HTML');

        ob_start();
        $this->controller->helpPage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }
}