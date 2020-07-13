<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Utility\ManualOrderUtility;

class AuthorController extends ActionController
{

    public function listAction(): void
    {

        // Collect authors
        $authors = RepositoryService::getAuthorRepository()->findByUids(GeneralUtility::intExplode(',', $this->settings['authors']));

        // Reorder posts
        if ($authors && $this->settings['ordering'] === 'manual') {
            $authors = ManualOrderUtility::order($this->settings['authors'], $authors->toArray());
        }

        // Pass variables to the fluid template
        $this->view->assignMultiple([
            'data' => $this->configurationManager->getContentObject()->data,
            'authors' => $authors
        ]);
    }
}
