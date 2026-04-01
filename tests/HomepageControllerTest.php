<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\HomepageController;

class HomepageControllerTest extends TestCase {
    private $controller;
    private $twigMock;
    private $modelMock;

    protected function setUp(): void {
        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
        $_SERVER = [];

        if (!defined('PHPUNIT_RUNNING')) {
            define('PHPUNIT_RUNNING', true);
        }

        $this->twigMock = $this->createMock(\Twig\Environment::class);
        $this->modelMock = $this->createMock(\App\Models\HomepageModel::class);
        $this->controller = new HomepageController($this->twigMock);

        $reflection = new ReflectionProperty($this->controller, 'Homepage_model');
        $reflection->setAccessible(true);
        $reflection->setValue($this->controller, $this->modelMock);
    }

    public function testWelcomePageRendersTwigOnGet() {
        $this->modelMock->method('getLatestOffers')->willReturn([]);
        $this->modelMock->method('getConseils')->willReturn([]);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('accueil.html.twig', $this->callback(function ($data) {
                return $data['show_cookie_banner'] === true
                    && $data['cookie_consent'] === null
                    && $data['cookie_value'] === null
                    && is_array($data['latest_offers'])
                    && is_array($data['conseils']);
            }))
            ->willReturn('HTML');

        ob_start();
        $this->controller->welcomePage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }
}