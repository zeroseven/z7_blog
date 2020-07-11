<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Controller;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use Zeroseven\Z7Blog\Domain\Model\Demand;
use Zeroseven\Z7Blog\Domain\Model\Pagination;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Service\RequestService;

class PostController extends ActionController
{

    /** @var array */
    protected $contentData;

    /** @var array */
    protected $requestArguments;

    public function initializeAction()
    {
        parent::initializeAction();

        /** @extensionScannerIgnoreLine */
        $this->contentData = $this->configurationManager->getContentObject()->data;
        $this->requestArguments = RequestService::getArguments();
    }

    protected function resolveView(): ViewInterface
    {
        // Get "original" view object
        $view = parent::resolveView();

        // Assign variables to all actions
        $view->assignMultiple([
            'requestArguments' => $this->requestArguments,
            'data' => $this->contentData
        ]);

        return $view;
    }

    protected function getDemand(bool $applySettings = null, bool $applyRequestArguments = null, ...$arguments): Demand
    {

        // Get request data
        $requestArguments = $applyRequestArguments !== false && (!isset($this->requestArguments['list_id']) || (int)$this->requestArguments['list_id'] === (int)$this->contentData['uid']) ? $this->requestArguments : [];

        // Create demand object with relevant arguments for filtering
        $demand = Demand::makeInstance()->setParameterArray(false, array_merge($applySettings === false ? [] : $this->settings, $requestArguments, ...$arguments));

        // Set list id
        if ($demand->getListId() === 0) {
            $demand->setListId($this->contentData['uid']);
        }

        return $demand;
    }

    protected function getPagination($posts, int $stage): Pagination
    {
        $itemsPerPage = $this->settings['items_per_stages'] ?: $this->settings['post']['list']['itemsPerStages'] ?: '6';
        return GeneralUtility::makeInstance(Pagination::class, $posts, $stage, $itemsPerPage, $this->settings['max_stages']);
    }

    public function listAction(): void
    {

        // Determine relevant arguments for filtering
        $demand = $this->getDemand(true);

        // Get posts depending on demand object
        $posts = RepositoryService::getPostRepository()->findByDemand($demand);

        // Pass variables to the fluid template
        $this->view->assignMultiple([
            'pagination' => $this->getPagination($posts, $demand->getStage()),
            'demand' => $demand
        ]);
    }

    public function listUncachedAction(): void
    {
        $this->forward('list');
    }

    public function filterAction(): void
    {

        // Create demand object
        $demand = $this->getDemand(true, false);

        // Add plugin settings of target list
        if ($listId = (int)$this->settings['list_id']) {

            // Get target content element
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
            $row = $queryBuilder
                ->select('pi_flexform', 'pid')
                ->from('tt_content')
                ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($listId, \PDO::PARAM_INT)))
                ->execute()
                ->fetch();

            // Define the target page
            $this->view->assign('pageUid', (int)$row['pid']);

            // Set flexform settings
            if ($flexform = $row['pi_flexform']) {
                $flexFormSettings = GeneralUtility::makeInstance(FlexFormService::class)->convertFlexFormContentToArray($flexform);
                $demand->setParameterArray(true, $flexFormSettings['settings']);

                // Override settings in variable provider
                $this->view->getRenderingContext()->getVariableProvider()->add('settings', array_merge($flexFormSettings['settings'], $this->settings));
            }
        }

        // Set request data to the demand
        if (!isset($this->requestArguments['list_id']) || (int)$this->requestArguments['list_id'] === (int)$this->settings['list_id']) {
            $demand->setParameterArray(false, $this->requestArguments);
        }

        // Pass variables to the content
        $this->view->assignMultiple([
            'categories' => RepositoryService::getCategoryRepository()->findAll(),
            'authors' => RepositoryService::getAuthorRepository()->findAll(),
            'topics' => RepositoryService::getTopicRepository()->findAll(),
            'tags' => RepositoryService::getTagRepository()->findAll($demand, true),
            'demand' => $demand
        ]);
    }
}
