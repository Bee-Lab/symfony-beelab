<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class DefaultController extends AbstractController
{
    public function homepage(): Response
    {
        return $this->render('default/homepage.html.twig');
    }
}
