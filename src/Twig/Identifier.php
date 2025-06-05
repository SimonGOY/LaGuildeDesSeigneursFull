<?php

namespace App\Twig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Identifier extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('identifier', [$this, 'identifier']),
        ];
    }
    
    public function identifier(string $hexString): string
    {
        // Convertir en majuscules et supprimer les espaces/tirets existants
        $clean = strtoupper(str_replace(['-', ' '], '', $hexString));
        
        // Découper en blocs de 4 caractères
        $blocks = str_split($clean, 4);
        
        // Joindre avec des tirets
        return implode('-', $blocks);
    }
}