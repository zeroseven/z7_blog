<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Zeroseven\Z7Blog\Service\RepositoryService;

class AuthorController extends ActionController
{

    public function listAction(): void
    {

        // Collect authors
        $authors = RepositoryService::getAuthorRepository()->findByUids(GeneralUtility::intExplode(',', $this->settings['authors']));

        // Pass variables to the fluid template
        $this->view->assignMultiple([
            'data' => $this->configurationManager->getContentObject()->data,
            'authors' => $authors
        ]);
    }
}
