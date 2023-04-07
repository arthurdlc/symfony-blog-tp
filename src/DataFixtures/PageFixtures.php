<?php

namespace App\DataFixtures;

use App\Entity\Page;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Contracts\Translation\TranslatorInterface;

class PageFixtures extends Fixture
{

    private $translator;
    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function load(ObjectManager $manager): void
    {
        $home = new Page();
        $home->setNumPage(1);
        $home->setTitle($this->translator->trans("Home"));
        $home->setText("<h1>" . $this->translator->trans("Home") . "</h1>");
        $manager->persist($home);

        $home = new Page();
        $home->setNumPage(2);
        $home->setTitle($this->translator->trans("Training Center"));
        $home->setText("<h1>" . $this->translator->trans("Training Center") . "</h1>");
        $manager->persist($home);

        $home = new Page();
        $home->setNumPage(3);
        $home->setTitle($this->translator->trans("Catalog"));
        $home->setText("<h1>" . $this->translator->trans("Catalog") . "</h1>");
        $manager->persist($home);

        $home = new Page();
        $home->setNumPage(4);
        $home->setTitle($this->translator->trans("Rate Formation"));
        $home->setText("<h1>" . $this->translator->trans("Rate Formation") . "</h1>");
        $manager->persist($home);

        $home = new Page();
        $home->setNumPage(5);
        $home->setTitle($this->translator->trans("Tutos"));
        $home->setText("<h1>" . $this->translator->trans("Tutos") . "</h1>");
        $manager->persist($home);

        $home = new Page();
        $home->setNumPage(6);
        $home->setTitle($this->translator->trans("Qualiopi Certificate"));
        $home->setText("<h1>" . $this->translator->trans("Qualiopi Certificate") . "</h1>");
        $manager->persist($home);

        $home = new Page();
        $home->setNumPage(7);
        $home->setTitle($this->translator->trans("Contact"));
        $home->setText("<h1>" . $this->translator->trans("Contact") . "</h1>");
        $manager->persist($home);

        $home = new Page();
        $home->setNumPage(8);
        $home->setTitle($this->translator->trans("Legal Informations"));
        $home->setText("<h1>" . $this->translator->trans("Legal Informations") . "</h1>");
        $manager->persist($home);

        $home = new Page();
        $home->setNumPage(9);
        $home->setTitle($this->translator->trans("Virgin"));
        $home->setText("<h1>" . $this->translator->trans("Virgin") . "</h1>");
        $manager->persist($home);
        
        $manager->flush();
    }
}
