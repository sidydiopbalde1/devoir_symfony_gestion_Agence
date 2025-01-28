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

    #[Route('/agence/add', name: 'app_agence_add', methods:["GET", "POST"])]
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $agence = new Agence();
        // ... form processing and validation
        $form = $this->createForm(AgenceType::class,$agence);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $agence = $form->getData();
            $agence->setNumero($agence->getNumero()); 
            $agence->setAdresse($agence->getAdresse());
            $agence->setTelephone($agence->getTelephone());
            $manager->persist($agence);
            $manager->flush();
            $this->addFlash('success','Votre agence a été ajouté avec succés');
            return $this->redirectToRoute('app_agences_liste');
        }
        return $this->render('agence/add.html.twig', [
          "form" => $form
        ]);
    }

    #[Route('/agence/search', name: 'app_agence_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entitymanager, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('q');
        $queryBuilder = $entitymanager->getRepository(Agence::class)->createQueryBuilder('a');
        
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

        return $this->render('agence/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}



// class AgenceController extends AbstractController
// {
//     #[Route('/agences', name: 'app_agences_index')]
//     public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
//     {
//         $searchForm = $this->createForm(AgenceSearchType::class);
//         $searchForm->handleRequest($request);
        
//         $editId = $request->query->get('edit');
//         $agence = $editId ? $em->getRepository(Agences::class)->find($editId) : new Agences();
        
//         $form = $this->createForm(AgenceType::class, $agence);
//         $form->handleRequest($request);

//         $queryBuilder = $em->getRepository(Agences::class)->createQueryBuilder('a');
        
//         if ($searchForm->isSubmitted() && $searchForm->isValid()) {
//             $search = $searchForm->get('search')->getData();
//             if ($search) {
//                 $queryBuilder
//                     ->where('a.Telephone LIKE :search')
//                     ->orWhere('a.Adresse LIKE :search')
//                     ->setParameter('search', '%'.$search.'%');
//             }
//         }
        
//         if ($form->isSubmitted() && $form->isValid()) {
//             if (!$editId) {
//                 $lastAgence = $em->getRepository(Agences::class)
//                     ->findOneBy([], ['id' => 'DESC']);
//                 $nextId = $lastAgence ? $lastAgence->getId() + 1 : 0;
//                 $agence->setNumero('AG_' . $nextId);
//                 $em->persist($agence);
//             }
            
//             $em->flush();
            
//             $this->addFlash('success', $editId ? 'Agence modifiée avec succès' : 'Agence créée avec succès');
//             return $this->redirectToRoute('app_agences_index');
//         }

//         $pagination = $paginator->paginate(
//             $queryBuilder->getQuery(),
//             $request->query->getInt('page', 1),
//             1
//         );

//         return $this->render('agence/index.html.twig', [
//             'pagination' => $pagination,
//             'searchForm' => $searchForm->createView(),
//             'form' => $form->createView(),
//             'editMode' => $editId !== null
//         ]);
//     }

//     #[Route('/agence/{id}', name: 'app_agence_delete', methods: ['POST'])]
//     public function delete(Request $request, Agences $agence, EntityManagerInterface $em): Response
//     {
//         if ($this->isCsrfTokenValid('delete'.$agence->getId(), $request->request->get('_token'))) {
//             $em->remove($agence);
//             $em->flush();
//             $this->addFlash('success', 'Agence supprimée avec succès');
//         }
        
//         return $this->redirectToRoute('app_agences_index');
//     }

//     #[Route('/agence/search', name: 'app_agence_search', methods: ['GET'])]
//     public function search(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
//     {
//         $search = $request->query->get('q');
//         $queryBuilder = $em->getRepository(Agences::class)->createQueryBuilder('a');
        
//         if ($search) {
//             $queryBuilder
//                 ->where('a.Telephone LIKE :search')
//                 ->orWhere('a.Adresse LIKE :search')
//                 ->setParameter('search', '%'.$search.'%');
//         }

//         $pagination = $paginator->paginate(
//             $queryBuilder->getQuery(),
//             $request->query->getInt('page', 1),
//             5
//         );

//         return $this->render('agence/_list.html.twig', [
//             'pagination' => $pagination
//         ]);
//     }
// }