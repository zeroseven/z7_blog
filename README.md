# z7_blog

## "Subextensions"

Damit die Erweiterung etliche Funktionen besitzt und trotzdem schlank und übersichtlich bleibt, lassen sich komplette Features als separate Erweiterung installieren.
Auf diese Art kannst du auch deine ganz eigenen "Subextensions" zum Blog erstellen und neue Funktionen schaffen.

Hier eine Übersicht empfohlener und kompatibler Erweiterungen:


* **[z7_blog_rss](https://gitlab.zeroseven.de/zeroseven/typo3-extensions/z7_blog_rss)** (RSS-Feeds über URL-Parameter erstellen)
* **z7_blog_comments** (In Kürze verfügbar)

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

NOTE: The CType can be overridden by the TCA configuration `contentLayoutKey`.

## Extend demand

**ext_localconf.php**

```php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['z7_blog']['Zeroseven\\Z7Blog\\Domain\\Demand\\PostDemand'] = \Namespace\ExtensionName\Demand\PostDemand::class;
```

```php
<?php declare(strict_types=1);

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
## Extend models

Es ist möglich domain models zu erweitern, indem du deine eigene `traits` hinterlegst.

**your_extension/Classes/Domain/Model/Post.php**:

```php
<?php

namespace Vendor\YourExtension\Domain\Model;

trait Post
{

    protected $lol = true;

    public function getLol(): bool
    {
        return $this->lol;
    }

}
```

**your_extension/ext_localconf.php**:

```php
<?php

call_user_func(static function () {

    // Load post trait collector instead of the "default" model 
    $extbaseObjectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class);
    $extbaseObjectContainer->registerImplementation(\Zeroseven\Z7Blog\Domain\Model\Post::class, \Zeroseven\Z7Blog\Domain\Model\TraitCollector\PostTraitCollector::class);
});

// Register trait
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['z7_blog']['traits'][\Zeroseven\Z7Blog\Domain\Model\Post::class][] = \Vendor\YourExtension\Domain\Model\Post::class;
```

## SEO-Konfiguration

Wenn Filter als Get-Parameter verwendet werden, ist es oftmals ratsam diese vom Crawler auszuschliens, vor allem bei der Mehrfachauswahl von Tags oder Topics können schnell tausende Kobinationen entstehen.

Beispiel: robots.txt

```
Disallow: *tx_z7blog_list%5Btopics%5D=*s
Disallow: *tx_z7blog_list%5Btags%5D=*
```

## Todo:

* Upgrades von div. TYPO3-Blog-Erweiterungen könnten über einen Upgrade-Wizard umgesetzt werden.
