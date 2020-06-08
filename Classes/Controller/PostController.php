<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use Zeroseven\Z7Blog\Domain\Model\Demand;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Domain\Model\Pagination;
use Zeroseven\Z7Blog\Service\RootlineService;

class PostController extends ActionController
{

    protected function getContentData(): array
    {
        /** @extensionScannerIgnoreLine */
        return $this->configurationManager->getContentObject()->data;
    }

    public function listAction(): void
    {

        // Get data of content element
        $data = $this->getContentData();

        // Get request data
        $requestArguments = [];
        if (!$this->request->hasArgument('list_id') || $this->request->hasArgument('list_id') && (int)$this->request->getArgument('list_id') === (int)$data['uid']) {
            $requestArguments = $this->request->getArguments();
        }

        // Determine relevant arguments for filtering
        $demand = Demand::makeInstance()->setParameterArray(false, $this->settings, $requestArguments);

        // Try to find the category if empty
        if (empty($demand->getCategory() === 0) && $category = RootlineService::findCategory()) {
            $demand->setCategory($category);
        }

        // Get posts depending on demand object
        $posts = RepositoryService::getPostRepository()->findAll($demand);

        // Create pagination object
        $itemsPerPage = $this->settings['itemsPerStage'] ?: $this->settings['post']['list']['defaultItemsPerStage'];
        $pagination = GeneralUtility::makeInstance(Pagination::class, $posts, $demand->getStage(), $itemsPerPage, $this->settings['maxStages']);

        // Pass variables to the fluid template
        $this->view->assignMultiple([
            'pagination' => $pagination,
            'demand' => $demand,
            'settings' => $this->settings,
            'requestArguments' => $requestArguments,
            'data' => $data
        ]);
    }

    public function listUncachedAction(): void
    {
        try {
            $this->forward('list');
        } catch (StopActionException $e) {
            return;
        }
    }
}
