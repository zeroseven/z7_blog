<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Zeroseven\Z7Blog\Domain\Demand\AuthorDemand;
use Zeroseven\Z7Blog\Service\RepositoryService;

class AuthorController extends ActionController
{
    public function listAction(): void
    {

        // Collect authors
        $demand = AuthorDemand::makeInstance()->setParameterArray(true, $this->settings);
        $demand->uids = AuthorDemand::makeInstance()->getL10nParents($demand);
        $authors = RepositoryService::getAuthorRepository()->findByDemand($demand);

        // Pass variables to the fluid template
        $this->view->assignMultiple([
            'data' => $this->configurationManager->getContentObject()->data,
            'authors' => $authors
        ]);
    }
}
