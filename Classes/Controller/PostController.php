<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use Zeroseven\Z7Blog\Domain\Model\Demand;
use Zeroseven\Z7Blog\Domain\Model\Pagination;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Service\RequestService;
use Zeroseven\Z7Blog\Service\RootlineService;

class PostController extends ActionController
{

    /** @var array */
    protected $requestArguments;

    public function __construct()
    {
        $this->requestArguments = RequestService::getArguments();
    }

    protected function getContentData(): array
    {
        /** @extensionScannerIgnoreLine */
        return $this->configurationManager->getContentObject()->data;
    }

    protected function getDemand(bool $applySettings = null, bool $applyRequestArguments = null, ...$arguments): Demand
    {

        // Determine relevant arguments for filtering
        $demand = Demand::makeInstance()->setParameterArray(false, array_merge(
            $applySettings === false ? [] : $this->settings,
            $applyRequestArguments === false ? [] : $this->requestArguments,
            ...$arguments
        ));

        // Try to find the category if empty
        if (empty($demand->getCategory()) && $category = RootlineService::findCategory()) {
            $demand->setCategory($category);
        }

        return $demand;
    }

    protected function resolveView(): ViewInterface
    {
        // Get "original" view object
        $view = parent::resolveView();

        // Assign variables to all actions
        $view->assignMultiple([
            'requestArguments' => $this->requestArguments,
            'data' => $this->getContentData()
        ]);

        return $view;
    }

    public function listAction(): void
    {

        // Get data of content element
        $data = $this->getContentData();

        // Get request data
        $applyRequestArguments = $this->request->hasArgument('list_id') === false || (int)$this->request->getArgument('list_id') === (int)$data['uid'];

        // Determine relevant arguments for filtering
        $demand = $this->getDemand(true, $applyRequestArguments);

        // Get posts depending on demand object
        $posts = RepositoryService::getPostRepository()->findAll($demand);

        // Create pagination object
        $itemsPerPage = $this->settings['itemsPerStage'] ?: $this->settings['post']['list']['defaultItemsPerStage'] ?: '6';
        $pagination = GeneralUtility::makeInstance(Pagination::class, $posts, $demand->getStage(), $itemsPerPage, $this->settings['maxStages']);

        // Pass variables to the fluid template
        $this->view->assignMultiple([
            'pagination' => $pagination,
            'demand' => $demand
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

    public function filterAction(): void
    {
        $this->view->assignMultiple([
            'categories' => RepositoryService::getCategoryRepository()->findAll(),
            'authors' => RepositoryService::getAuthorRepository()->findAll(),
            'topics' => RepositoryService::getTopicRepository()->findAll(),
            'tags' => RepositoryService::getTagRepository()->findAll(),
            'demand' => $this->getDemand(true, true)
        ]);
    }
}
