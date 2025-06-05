<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GenderSign extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('gender', [$this, 'gender']),
        ];
    }

    public function gender(string $kind): string
    {
        $url = 'Seigneur' === $kind || 'Ennemi' === $kind ? 'https://run.as/skwkgk' : 'https://run.as/6h9mzj';
        
        return '<img height="24" src="' . $url . '">';
    }
}