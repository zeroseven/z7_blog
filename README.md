# z7_blog

## Add details of the blogpost to the template

If you need information about the post on each detail page, there's no need to maintain each one individually. Use the following TypoScript to add blog content to each post at a place of your liking.

```typo3_typoscript
page.100 = USER
page.100 {
    userFunc = Zeroseven\Z7Blog\Utility\PostInfoRenderUtility->renderUserFunc
    file = EXT:z7_blog/Resources/Private/Partials/Post/Info/Summary.html
    settings {
        pass.any = settings
        to = the template
    }
}
```

… or render the info by a viewHelper in your template:

```fluid
<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers" xmlns:blog="http://typo3.org/ns/Zeroseven/Z7Blog/ViewHelpers" data-namespace-typo3-fluid="true">
    <main>
        <h1>{page.title}</h1>
        <blog:postInfo file="EXT:z7_blog/Resources/Private/Partials/Post/Info/Summary.html" />
    </main>
</html>
```

… or render a custom content element:

```typo3_typoscript
tt_content.custom_blogpost_info =< lib.contentElement
tt_content.custom_blogpost_info {
    templateName = Generic

    20 = USER
    20 {
        userFunc = Zeroseven\Z7Blog\Utility\PostInfoRenderUtility->renderUserFunc
        file = EXT:z7_blog/Resources/Private/Partials/Post/Info/Summary.html
    }
}
```

## Use different layouts

Add the selectable layouts for the editor by the TSconfig.
Inside the Fluidtemplate you can use some conditions, depending on the variable `{settings.layout}`.

```
tx_z7blog.content.[CType].layouts {
  sidebar = Sidebar-Teaser
  archive = Archive
}
```

## Extend demand

**ext_localconf.php**

```php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['z7_blog']['Zeroseven\\Z7Blog\\Domain\\Demand\\PostDemand'] = \Namespace\ExtensionName\Demand\PostDemand::class;
```

```php
<?php
declare(strict_types=1);

namespace Namespace\ExtensionName\Demand;

class PostDemand extends Zeroseven\Z7Blog\Domain\Demand\PostDemand
{
    /** @var bool */
    public $navHide = false;   

    /** @var array */
    public $keywords = [];   
}
```

## Update from extension "blogpages"

Diese mysql queries kannst du verwenden, um einige Tabellen und Felder aus der Erweiterung "blogpages" upzudaten. Dabei ist es wichtig, dass du das **vor dem Installieren** ausführst. 

**ACHTUNG:** Erstelle zuerst ein vollständiges Backup der Datenbank und schau dir genau an, was du machst. Die Queries sind keine Garantie, sondern nur eine Hilfestellung. Auch ist damit nicht alles gelöst. Beispielsweise die User-Berechtigungen, Flexform-Einstellungen oder andere Verknüpfungen müssen noch händisch ergänzt werden. 

```mysql
-- Update authors
RENAME TABLE `tx_blogpages_domain_model_author` TO `tx_z7blog_domain_model_author`;
UPDATE `sys_file_reference` SET `tablenames` = 'tx_z7blog_domain_model_author' WHERE `tablenames` = 'tx_blogpages_domain_model_author';

-- The reincarnation of the topics (was called "tags" in previous life)
ALTER TABLE `pages` CHANGE `post_tags` `post_topics` INT(11) unsigned DEFAULT '0' NOT NULL;
RENAME TABLE `tx_blogpages_domain_model_tag` TO `tx_z7blog_domain_model_topic`;
RENAME TABLE `tx_blogpages_post_tag_mm` TO `tx_z7blog_post_topic_mm`;

-- Move the relations between the posts
ALTER TABLE `pages` CHANGE `post_related` `post_relations_to` INT(10) UNSIGNED NOT NULL DEFAULT '0';
RENAME TABLE `tx_blogpages_post__mm` TO `tx_z7blog_post_mm`;

-- Update content elements
UPDATE `tt_content` SET `CType` = 'z7blog_list', `hidden` = '1', `rowDescription` = CONCAT('The content type was changed and the element disabled while upgrading the blog extension.\n\n', rowDescription) WHERE `CType` = 'blogpages_list';
UPDATE `tt_content` SET `CType` = 'z7blog_filter', `hidden` = '1', `rowDescription` = CONCAT('The content type was changed and the element disabled while upgrading the blog extension.\n\n', rowDescription) WHERE `CType` = 'blogpages_filter';
```

## Todo:

* Upgrades von div. TYPO3-Blog-Erweiterungen könnten über einen Upgrade-Wizard umgesetzt werden.
