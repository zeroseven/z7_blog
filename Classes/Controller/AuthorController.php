<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Zeroseven\Z7Blog\Domain\Demand\AuthorDemand;
use Zeroseven\Z7Blog\Domain\Repository\AuthorRepository;

class AuthorController extends ActionController
{
    private $authorRepository;

    public function injectAuthorRepository(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function listAction(): void
    {

        // Collect authors
        $demand = AuthorDemand::makeInstance()->setParameterArray(true, $this->settings);
        $authors = $this->authorRepository->findByDemand($demand);

        // Pass variables to the fluid template
        // false positive: deprecated (renamed) AbstractContentObject::getContentObject()
        // https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/8.0/Deprecation-68748-DeprecateAbstractContentObjectgetContentObject.html
        // @extensionScannerIgnoreLine
        $this->view->assignMultiple([
            'data' => $this->configurationManager->getContentObject()->data,
            'authors' => $authors
        ]);
    }
}
