<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Service\SettingsService;
use Zeroseven\Z7Blog\Utility\GlobalUtility;

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
    
    /**
     * Method initialize
     *
     * @return void
     */
    protected function initialize(): void
    {
        $this->pluginConfiguration = SettingsService::getPluginConfiguration();

        $this->view = GeneralUtility::makeInstance(StandaloneView::class);

        $extbaseRequestParams = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters::class);
        $extbaseRequestParams->setControllerExtensionName('Z7Blog');
        $extbaseRequestParams->setControllerName('Post');

        $serverRequest = GlobalUtility::getRequest();
        $extbaseRequest = GeneralUtility::makeInstance(Request::class, $serverRequest->withAttribute('extbase', $extbaseRequestParams));

        $this->view->setRequest($extbaseRequest);
        $this->view->setTemplateRootPaths($this->pluginConfiguration['view']['templateRootPaths'] ?? []);
        $this->view->setPartialRootPaths($this->pluginConfiguration['view']['partialRootPaths'] ?? []);
        $this->view->setLayoutRootPaths($this->pluginConfiguration['view']['layoutRootPaths'] ?? []);
        $this->view->setFormat('html');
    }
    
    /**
     * Method render
     *
     * @param string $templateNameAndFilePath
     * @param array $settings
     * @param Post $post
     *
     * @return string
     */
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
            // @extensionScannerIgnoreLine
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
            'data' => $this->cObj->data ?? [],
        ]);

        return $this->view->render();
    }
    
    /**
     * Method renderUserFunc
     *
     * @param string $content
     * @param array $conf
     *
     * @return string
     */
    public function renderUserFunc(string $content, array $conf): string
    {
        $settings = isset($conf['settings.']) ? GeneralUtility::makeInstance(TypoScriptService::class)->convertTypoScriptArrayToPlainArray($conf['settings.']) : [];

        return ($content ?: '') . $this->render($conf['file'] ?? '', $settings);
    }
}
