<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Entity\Agences;
use App\Form\AgenceType;
use App\Form\AgenceSearchType;
use App\Repository\AgenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgenceController extends AbstractController
{
    #[Route('/agence/liste', name: 'app_agences_liste',methods:["GET"])]
    public function index(AgenceRepository $agenceRepository,Request $request, EntityManagerInterface $entitymanager, PaginatorInterface $paginator): Response
    {
        $searchForm = $this->createForm(AgenceType::class);
        $searchForm->handleRequest($request);
        $agence= new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
        $agences=  $agenceRepository->findAll();
        
        $form->handleRequest($request);

        $queryBuilder = $entitymanager->getRepository(Agence::class)->createQueryBuilder('a');
        
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->get('search')->getData();
            if ($search) {
                $queryBuilder
                    ->where('a.Telephone LIKE :search')
                    ->orWhere('a.Adresse LIKE :search')
                    ->setParameter('search', '%'.$search.'%');
            }
        }
        
      

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('agence/index.html.twig', [
            'agences' => $pagination,
            'searchForm' => $searchForm->createView(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/agence/{id}', name: 'app_agence_delete', methods: ['POST'])]
    public function delete(Request $request, Agence $agence, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agence->getId(), $request->request->get('_token'))) {
            $em->remove($agence);
            $em->flush();
            $this->addFlash('success', 'Agence supprimée avec succès');
        }
        
        return $this->redirectToRoute('app_agences_index');
    }

    #[Route('/agence/search', name: 'app_agence_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('q');
        $queryBuilder = $em->getRepository(Agence::class)->createQueryBuilder('a');
        
        if ($search) {
            $queryBuilder
                ->where('a.Telephone LIKE :search')
                ->orWhere('a.Adresse LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('agence/_list.html.twig', [
            'pagination' => $pagination
        ]);
    }
}