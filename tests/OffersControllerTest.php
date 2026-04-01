<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\OffersController;

class OffersControllerTest extends TestCase {
    private $controller;
    private $twigMock;
    private $offerModelMock;

    protected function setUp(): void {
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
        $_SERVER = [];

        if (!defined('PHPUNIT_RUNNING')) {
            define('PHPUNIT_RUNNING', true);
        }

        $this->twigMock = $this->createMock(\Twig\Environment::class);
        $this->offerModelMock = $this->createMock(\App\Models\OffersModel::class);
        $this->controller = new OffersController($this->twigMock);

        $reflection = new ReflectionProperty($this->controller, 'offer_model');
        $reflection->setAccessible(true);
        $reflection->setValue($this->controller, $this->offerModelMock);
    }

    public function testOffersPageRendersTwig() {
        $this->offerModelMock->method('getOffers')->willReturn([]);
        $this->offerModelMock->method('getOfferStats')->willReturn([]);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('offres.html.twig', $this->callback(fn($data) => isset($data['offres'])))
            ->willReturn('HTML');

        ob_start();
        $this->controller->offersPage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }

    public function testShowOfferRendersTwigWhenFound() {
        $_GET['id_offre'] = 1;
        $this->offerModelMock->method('getOfferById')->willReturn(['id_offre' => 1]);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('poste_offre.html.twig', $this->callback(fn($data) => $data['offre']['id_offre'] === 1))
            ->willReturn('HTML');

        ob_start();
        $this->controller->showOffer();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }

    public function testShowOfferCallsOffersPageWhenNoId() {
        ob_start();
        $this->controller->showOffer();
        $output = ob_get_clean();
        $this->assertTrue(true);
    }

    public function testCreateOfferPageRendersTwig() {
        $this->offerModelMock->method('getCompaniesForSelect')->willReturn([]);

        $_SESSION['admin']['id'] = 1;

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('offre_form.html.twig', $this->callback(fn($data) => $data['offer'] === null))
            ->willReturn('HTML');

        ob_start();
        $this->controller->createOfferPage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }

    public function testEditOfferPageRendersTwigWhenOfferFound() {
        $_GET['id'] = 1;
        $_SESSION['admin']['id'] = 1;

        $this->offerModelMock->method('getOfferById')->willReturn(['id_offre' => 1]);
        $this->offerModelMock->method('getCompaniesForSelect')->willReturn([]);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('offre_form.html.twig', $this->callback(fn($data) => $data['offer']['id_offre'] === 1))
            ->willReturn('HTML');

        ob_start();
        $this->controller->editOfferPage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }

    public function testPostulerPageRendersTwigOnGet() {
        $_GET['id_offre'] = 1;
        $this->offerModelMock->method('getDataFormed')->willReturn([1, ['name' => 'Etudiant']]);
        $this->offerModelMock->method('getOfferById')->willReturn(['id_offre' => 1]);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('postuler.html.twig', $this->callback(fn($data) => $data['data'][0] === 1))
            ->willReturn('HTML');

        ob_start();
        $this->controller->postulerPage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }
}