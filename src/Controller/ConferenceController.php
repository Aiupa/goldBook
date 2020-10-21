<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(ConferenceRepository $conferenceRepository)
    {
        return $this->render('conference/index.html.twig', 
        ['conferences' => $conferenceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/conference/{slug}", name="conference")
     */
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository, ConferenceRepository $conferenceRepository)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handlerequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $comment->setConference($conference);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return $this->render('conference/show.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form->createView(),
            ]);
    }
}
