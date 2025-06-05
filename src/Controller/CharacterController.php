<?php

namespace App\Controller;

use App\Entity\Character;
use App\Form\CharacterForm;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use DateTime;

#[Route('/character')]
final class CharacterController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    #[Route('/', name: 'app_character_index', methods: ['GET'])]
    public function index(CharacterRepository $characterRepository): Response
    {
        return $this->render('character/index.html.twig', [
            'characters' => $characterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_character_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $character = new Character();
        $form = $this->createForm(CharacterForm::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Mieux dans un service
            $character->setIdentifier(hash('sha1', uniqid()));
            $character->setSlug($this->slugger->slug($character->getName())->lower());
            $character->setCreation(new DateTime());
            $character->setModification(new DateTime());

            $entityManager->persist($character);
            $entityManager->flush();

            return $this->redirectToRoute('app_character_show', [
                'id' => $character->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character/new.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_character_show', methods: ['GET'])]
    public function show(Character $character): Response
    {
        return $this->render('character/show.html.twig', [
            'character' => $character,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_character_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CharacterForm::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mieux dans un service
            $character->setSlug($this->slugger->slug($character->getName())->lower());
            $character->setModification(new DateTime());

            $entityManager->persist($character);
            $entityManager->flush();

            return $this->redirectToRoute('app_character_show', [
                'id' => $character->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character/edit.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_character_delete', methods: ['POST'])]
    public function delete(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($character);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/health/{maxHealth}', name: 'app_character_health', requirements: ['maxHealth' => '\d+'], methods: ['GET'])]
    public function health(CharacterRepository $characterRepository, int $maxHealth): Response
    {
        $characters = $characterRepository->findByHealthLevelOrBelow($maxHealth);

        return $this->render('character/health.html.twig', [
            'characters' => $characters,
            'maxHealth' => $maxHealth,
        ]);
    }
}
