<?php
namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploadHelper {
    private $slugger;


    public function __construct( SluggerInterface $slugger){
        $this->slugger = $slugger;
    }


    public function uploadImage($form){
        $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger',$translator->trans('An error is append') . $e->getMessage());
                }
                $formation->setimageFilename($newFilename);
            }
    }
}