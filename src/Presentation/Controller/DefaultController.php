<?php

namespace Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: Request::METHOD_GET, priority: 1)]
    public function homepage(): Response
    {
        return $this->render('default/homepage.html.twig');
    }
}
