<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\StudentManagementController;

class StudentManagementControllerTest extends TestCase {
    private $controller;
    private $twigMock;
    private $studentModelMock;

    protected function setUp(): void {
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
        $_SERVER = [];

        $this->twigMock = $this->createMock(\Twig\Environment::class);
        $this->studentModelMock = $this->createMock(\App\Models\StudentManagementModel::class);
        $this->controller = new StudentManagementController($this->twigMock);

        $reflection = new ReflectionProperty($this->controller, 'student_management_model');
        $reflection->setAccessible(true);
        $reflection->setValue($this->controller, $this->studentModelMock);
    }

    public function testStudentsPageRendersTwig() {
        $_SESSION['admin']['id'] = 1;
        $this->studentModelMock->method('getStudents')->willReturn([]);
        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('admin_students.html.twig', $this->callback(fn($data) => isset($data['students'])))
            ->willReturn('HTML');

        ob_start();
        $this->controller->studentsPage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }

    public function testStudentCreatePageRendersTwig() {
        $_SESSION['admin']['id'] = 1;
        $this->studentModelMock->method('getPilotsForSelect')->willReturn([]);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('admin_student_form.html.twig', $this->callback(fn($data) => $data['student'] === null))
            ->willReturn('HTML');

        ob_start();
        $this->controller->studentCreatePage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }

    public function testStudentEditPageRendersTwigWhenStudentFound() {
        $_GET['id'] = 1;
        $_SESSION['admin']['id'] = 1;

        $this->studentModelMock->method('getStudentById')->willReturn(['id' => 1]);
        $this->studentModelMock->method('getPilotsForSelect')->willReturn([]);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('admin_student_form.html.twig', $this->callback(fn($data) => $data['student']['id'] === 1))
            ->willReturn('HTML');

        ob_start();
        $this->controller->studentEditPage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }
}