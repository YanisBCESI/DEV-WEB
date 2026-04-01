<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\ConseilsController;

class ConseilsControllerTest extends TestCase {

    private $controller;
    private $twigMock;
    private $modelMock;

    protected function setUp(): void {
        $_GET = [];

        $this->twigMock = $this->createMock(\Twig\Environment::class);
        $this->modelMock = $this->createMock(\App\Models\ConseilsModel::class);

        $this->controller = new ConseilsController($this->twigMock);

        // injection du mock model
        $reflection = new ReflectionProperty($this->controller, 'conseils_model');
        $reflection->setAccessible(true);
        $reflection->setValue($this->controller, $this->modelMock);
    }
    public function testGetDataReturnsArray() {
        $this->modelMock
            ->method('getFromDataBase')
            ->willReturn(['test']);

        $result = $this->controller->getData();

        $this->assertEquals(['test'], $result);
    }
    public function testConseilsPageDisplaysList() {

        $fakeData = [
            ["id" => 1, "titre" => "Conseil 1"],
            ["id" => 2, "titre" => "Conseil 2"]
        ];

        $this->modelMock
            ->method('getFromDataBase')
            ->willReturn($fakeData);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with(
                "conseil.html.twig",
                ["conseils" => $fakeData]
            )
            ->willReturn("HTML");

        ob_start();
        $this->controller->conseilsPage();
        $output = ob_get_clean();

        $this->assertEquals("HTML", $output);
    }
}