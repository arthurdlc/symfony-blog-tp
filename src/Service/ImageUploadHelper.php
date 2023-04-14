<?php
namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageUploadHelper {
    private $slugger;
    private $params;

    public function __construct( SluggerInterface $slugger, ParameterBagInterface $params){
        $this->slugger = $slugger;
        $this->params = $params;
    }


    public function uploadImage($form, $formation) : String { 
        $errorMessage = "";
        $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->params->get('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger',$translator->trans('An error is append') . $e->getMessage());
                }
                $formation->setimageFilename($newFilename);
            }
            return $errorMessage;
    }
}