<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Utility;

use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Service\SettingsService;

class PostInfoRenderUtility
{

    /**
     * Back reference to the parent content object
     * This has to be public as it is set directly from TYPO3
     *
     * @var ContentObjectRenderer
     */
    public $cObj;

    /** @var StandaloneView */
    protected $view;

    /** @var array */
    protected $pluginConfiguration;

    protected function initialize(): void
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->pluginConfiguration = SettingsService::getPluginConfiguration();

        $this->view = $objectManager->get(StandaloneView::class);

        $this->view->getRequest()->setControllerExtensionName('Z7Blog');
        $this->view->getRequest()->setControllerName('Post');
        $this->view->setTemplateRootPaths($this->pluginConfiguration['view']['templateRootPaths'] ?? []);
        $this->view->setPartialRootPaths($this->pluginConfiguration['view']['partialRootPaths'] ?? []);
        $this->view->setLayoutRootPaths($this->pluginConfiguration['view']['layoutRootPaths'] ?? []);
        $this->view->setFormat('html');
    }

    public function render(string $templateNameAndFilePath, array $settings = null, Post $post = null): string
    {
        // Abort if page is not a post
        if ($post === null && (int)($GLOBALS['TSFE']->page['doktype'] ?? 0) !== Post::DOKTYPE) {
            return '';
        }

        // Initialize stuff
        $this->initialize();

        // Get the post
        try {
            $post = RepositoryService::getPostRepository()->findByUid($GLOBALS['TSFE']->id ?? 0);
        } catch (AspectNotFoundException $e) {
            $post = null;
        }

        // Set Template
        $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templateNameAndFilePath));

        // Assign variables to the view
        $this->view->assignMultiple([
            'post' => $post,
            'settings' => array_merge($this->pluginConfiguration['settings'] ?? [], $settings ?: []),
            'data' => $this->cObj->data
        ]);

        return $this->view->render();
    }

    public function renderUserFunc(string $content, array $conf): string
    {
        $settings = is_array($conf['settings.'] ?? null) ? GeneralUtility::makeInstance(TypoScriptService::class)->convertTypoScriptArrayToPlainArray($conf['settings.']) : [];

        return ($content ?: '') . $this->render($conf['file'] ?? '', $settings);
    }
}
