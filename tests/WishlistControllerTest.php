<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\WishlistController;

class WishlistControllerTest extends TestCase {
    private $controller;
    private $twigMock;
    private $wishlistModelMock;

    protected function setUp(): void {
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
        $_SERVER = [];

        $this->twigMock = $this->createMock(\Twig\Environment::class);
        $this->wishlistModelMock = $this->createMock(\App\Models\WishlistModel::class);
        $this->controller = new WishlistController($this->twigMock);

        $reflection = new ReflectionProperty($this->controller, 'wishlist_model');
        $reflection->setAccessible(true);
        $reflection->setValue($this->controller, $this->wishlistModelMock);
    }

    public function testWishlistPageRendersTwig() {
        $_SESSION['student']['id'] = 42;
        $this->wishlistModelMock->method('getWishlistByStudentId')->willReturn([]);
        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('wishlist.html.twig', $this->callback(fn($data) => isset($data['wishlist'])))
            ->willReturn('HTML');

        ob_start();
        $this->controller->wishlistPage();
        $output = ob_get_clean();

        $this->assertEquals('HTML', $output);
    }
}