<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(static function () {

    // Register icons
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'content-z7blog-filter',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:z7_blog/Resources/Public/Icons/content-z7blog-filter.svg']
    );
    $iconRegistry->registerIcon(
        'content-z7blog-list ',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:z7_blog/Resources/Public/Icons/content-z7blog-list.svg']
    );
    $iconRegistry->registerIcon(
        'content-z7blog-static',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:z7_blog/Resources/Public/Icons/content-z7blog-static.svg']
    );
    $iconRegistry->registerIcon(
        'content-z7blog-authors',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:z7_blog/Resources/Public/Icons/content-z7blog-authors.svg']
    );
    $iconRegistry->registerIcon(
        'apps-pagetree-blogpost',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:z7_blog/Resources/Public/Icons/apps-pagetree-blogpost.svg']
    );
    $iconRegistry->registerIcon(
        'apps-pagetree-blogcategory',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:z7_blog/Resources/Public/Icons/apps-pagetree-blogcategory.svg']
    );
    $iconRegistry->registerIcon(
        'apps-pagetree-blogpost-hideinmenu',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:z7_blog/Resources/Public/Icons/apps-pagetree-blogpost-hideinmenu.svg']
    );
    $iconRegistry->registerIcon(
        'apps-pagetree-blogcategory-hideinmenu',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:z7_blog/Resources/Public/Icons/apps-pagetree-blogcategory-hideinmenu.svg']
    );

});
