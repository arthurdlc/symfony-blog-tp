<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Formation;
use App\Form\Formation1Type;
use App\Service\ImageUploadHelper;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/formation')]
class FormationController extends AbstractController
{
    #[Route('/{id}/duplicate', name: 'app_formation_duplicate', methods: ['GET', 'POST'])]
    public function duplicate(Request $request, ImageUploadHelper $imageUploader, Formation $formation, FormationRepository $formationRepository, SluggerInterface $slugger, TranslatorInterface $translator ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $formation2=new Formation();
        $formation2->setCreatedAt($formation->getCreatedAt());
        $formation2->setCreatedBy($formation->getCreatedBy());
        $formation2->setContent($formation->getContent());
        $formation2->setDescription($formation->getDescription());

        $formation2->setCapacity($formation->getCapacity());
        $formation2->setStartDate($formation->getStartDate());
        $formation2->setEndDate($formation->getEndDate());
        $formation2->setImageFileName($formation->getImageFileName());
        $formation2->setName($formation->getName());
        $formation2->setPrice($formation->getPrice());

        $formationRepository->save($formation2, true);
        $this->addFlash('success', $translator->trans('the formation is copied'));


        return $this->redirectToRoute('app_formation_index');
    }

    #[Route('/pdf/{id}', name: 'app_formation_pdf', methods: ['GET'])]
    public function pdf(Formation $formation): Response
    {
        $pdf = new \TCPDF();

        $pdf->SetAuthor('SIO1');
        $pdf->SetTitle($formation->getName());
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->setCellMargins(1, 1, 1, 1);

        $pdf->AddPage();
        $pdf->SetFont('helvetica','',14);
        $pdf->SetFillColor(0, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(185, 10, $formation->getName(), 1, 'P', 1, 0, '', '', true);

        $pdf->SetXY(10,25);
        $pdf->SetFillColor(0, 200, 127);
        $pdf->SetFont('times','',14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(185, 10, "A partir du : " . $formation->getStartDate()->format('d M Y'), 1, 'P', 1, 1, '', '', true);
        $pdf->MultiCell(185, 10, $formation->getCapacity() . ' places ', 1, 'P', 1, 1, '', '', true);
        $pdf->MultiCell(185, 10, "Tarif : " . $formation->getPrice(), 1, 'P', 1, 1, '', '', true);
        $pdf->WriteHTML($formation->getContent());

        return $pdf->Output('fcpro-formation-'.$formation->getId() .'.pdf');
    }

    #[Route('/futur', name: 'app_formation_futur', methods: ['GET'])]
    public function futur(FormationRepository $formationRepository): Response
    {
        $formationsPerThree = array();

        $formations = $formationRepository->findAllInTheFutur();

        $j=1; $i=0;
        foreach ($formations as $formation) {
            $i++;
            if ( $i>3 ) {
                $j++; $i=1;
            }
            $formationsPerThree[$j][$i] = $formation;
        }
        dump($formations);
        dump($formationsPerThree);

        return $this->render('formation/futur.html.twig', [
            'tousLespaquets' => $formationsPerThree,
        ]);
    }

    #[Route('/catalog', name: 'app_formation_catalog', methods: ['GET'])]
    public function catalog(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/catalog.html.twig', [
            'formations' => $formationRepository->findAllInTheFutur(),
        ]);
    }

    #[Route('/', name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $formation = new Formation();
        $formation->setCreatedAt(new DateTimeImmutable());
        $formation->setCreatedBy($this->getUser());

        $form = $this->createForm(Formation1Type::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $errorMessage = $imageUploader->uploadImage($form, $formation) ; 
            if (!empty($errorMessage)){
                $this->addFlash('danger', $translator->trans('An error is append; ') . $errorMessage);
            }
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ImageUploadHelper $imageUploader, Formation $formation, FormationRepository $formationRepository, SluggerInterface $slugger, TranslatorInterface $translator ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(Formation1Type::class, $formation);
        $form->handleRequest($request);

        $errorMessage = $imageUploader->uploadImage($form, $formation);
    
        if ($form->isSubmitted() && $form->isValid()) {
            ////////////////////////////////////////////////////////////////
             $this->addFlash('danger', $translator->trans('An error is append; ') . $errorMessage);        
            ////////////////////////////////////////////////////////////////
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, FormationRepository $formationRepository ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $formationRepository->remove($formation, true);
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
