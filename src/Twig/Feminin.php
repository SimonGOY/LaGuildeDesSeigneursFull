<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Feminin extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('feminin', [$this, 'feminin']),
        ];
    }

    public function feminin(string $kind, string $text): string
    {
        if ('Dame' === $kind || 'Tourmenteuse' === $kind) {
            $text = 'un' === $text ? 'une' : $text;
            $text = 'fort' === $text ? 'forte' : $text;
        }
        return $text;
    }
}