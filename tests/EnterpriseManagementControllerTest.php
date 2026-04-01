<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\EnterpriseManagementController;

class EnterpriseManagementControllerTest extends TestCase {

    private $controller;
    private $twigMock;
    private $modelMock;

    protected function setUp(): void {
        $_GET = [];
        $_POST = [];
        $_SESSION = [];

        $this->twigMock = $this->createMock(\Twig\Environment::class);
        $this->modelMock = $this->createMock(\App\Models\EnterpriseManagementModel::class);

        $this->controller = new EnterpriseManagementController($this->twigMock);

        $reflection = new ReflectionProperty($this->controller, 'enterprise_management_model');
        $reflection->setAccessible(true);
        $reflection->setValue($this->controller, $this->modelMock);
    }
    public function testCompaniesPageDisplaysData() {

        $_GET["search"] = "test";

        $fakeCompanies = [["id" => 1]];

        $this->modelMock
            ->method('getCompanies')
            ->with("test")
            ->willReturn($fakeCompanies);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with(
                "entreprises.html.twig",
                $this->callback(function ($data) use ($fakeCompanies) {
                    return $data["companies"] === $fakeCompanies
                        && $data["search"] === "test";
                })
            )
            ->willReturn("HTML");

        ob_start();
        $this->controller->companiesPage();
        $output = ob_get_clean();

        $this->assertEquals("HTML", $output);
    }
    public function testCompanyDetailPageDisplaysCompany() {

        $_GET["id"] = 1;

        $fakeCompany = ["id" => 1];

        $this->modelMock
            ->method('getCompanyById')
            ->willReturn($fakeCompany);

        $this->modelMock
            ->method('getManagementEvaluationsByCompanyId')
            ->willReturn([]);

        $this->modelMock
            ->method('getManagementEvaluationForCompany')
            ->willReturn(null);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->willReturn("DETAIL");

        ob_start();
        $this->controller->companyDetailPage();
        $output = ob_get_clean();

        $this->assertEquals("DETAIL", $output);
    }
}