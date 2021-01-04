# TYPO3 blog extension

## :interrobang: Why another blog extension?

### For the best developers

We love TYPO3 developers and TYPO3 developers love this extension.
It’s never been so easy to bring a blog to a TYPO3 page, to customize it and put information where they belong. Extend the Extbase models, synchronize the content on post pages or invent new filter arguments for ViewHelpers or the repository with just a bunch of lines of code. It’s totally up to you.

Want a RSS-Feed? Or a comments function? This extension is built highly modular:
Whole features are easily installed or removed or you develop your very own features for the blog. This way, you’ll always only have the exact amount of features and configuration that you really need. Speaking of need: you really need this extension!

Oh by the way, we support multi language and multi domain setups of course.


### For the proficient editors

We love TYPO3 editors and TYPO3 editors love this extension.
It’s never been so easy to maintain and manage blog posts and categories, to assign tags and to output all that filtered and controlled on a TYPO3 page. Since this works as usual with pages and content elements you can start instantly. Furthermore the backend supports you with automated post sorting in the page tree, autocompletion and many more useful tools to help you get the job done fast.


### For the successful SEOs

We love SEO people and SEO people love this extension.
A great opportunity to generate content for your website. Since all the posts are „regular“ TYPO3 pages, all the possibilities the TYPO3 core offers are available here as well. This way, you don’t need to worry about open graph data, sitemaps, canonical or meta tags like with record based extensions in this blog. It’s all there. On top, we’ve added structured data for all the posts just like that.


## :lollipop: Feature overview

* Based on TYPO3 pages
* Supports multi domain setup
* Extended filter plugin
* Structured data
* Variable ajax pagination
* Autocomplete tags in the backend
* Automated sorting in page tree
* Custom plugin layouts
* Custom conditions for your fluid templates
* Modular sub extensions available:
    * RSS-Feed
    * Comment function

## :sparkles: "Subextensions"

Like mentioned: For this extension to have a huge set of features but to not bloat it, these features can be installed or removed with our concept of subextensions.
This way, you can also add your own subextensions to the blog.

Here's an overview of existing subextensions:

Extensionkey | Description | Installation
--- | --- | ---
**[z7_blog_rss](https://github.com/zeroseven/z7_blog_rss)** | Creates a RSS feed via URL parameters | `composer req zeroseven/z7-blog-rss`
**[z7_blog_comments](https://github.com/zeroseven/z7_blog_comments)** | Enhances the blog with a comment function | `composer req zeroseven/z7-blog-comments`

## :wrench: Installation

Get this extension via `composer req zeroseven/z7-blog`.

## :gear: Setup

### Add post details to the template

If you need information about the post on each blog post, there's no need to maintain each one individually. Use the following TypoScript to add blog content to each post at a place of your liking.

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

… or render the info by a ViewHelper in your template:

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

So you see, there are several ways to achieve what you want. Feel free to choose what suits you best!

### Use different layouts

Add selectable layouts for the editor via TSconfig.
Inside the Fluidtemplate you can use conditions, depending on the variable `{settings.layout}`.

```
tx_z7blog.content.[CType].layouts {
  sidebar = Sidebar-Teaser
  archive = Archive
}
```

:warning: The CType can be overridden by the TCA configuration `contentLayoutKey`.

### Extend models and demands classes

It's possible to extend a domain model or a demand class, by adding your own `traits`.

**your_extension/Classes/Domain/Traits/PostModel.php**:

```php
<?php declare(strict_types=1);

namespace Vendor\YourExtension\Domain\Traits;

trait PostModel
{

    protected $lol = true;

    public function getLol(): bool
    {
        return $this->lol;
    }

}
```

**your_extension/Classes/Domain/Traits/PostDemand.php**:

```php
<?php declare(strict_types=1);

namespace Vendor\YourExtension\Domain\Traits;

class PostDemand
{
    /** @var bool */
    public $navHide = false;

    /** @var array */
    public $keywords = [];
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
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['z7_blog']['traits'][\Zeroseven\Z7Blog\Domain\Model\Post::class][] = \Vendor\YourExtension\Domain\Traits\PostModel::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['z7_blog']['traits'][\Zeroseven\Z7Blog\Domain\Demand\PostDemand::class][] = \Vendor\YourExtension\Domain\Traits\PostDemand::class;
```

### JavaScript events

The extension triggers various useful JavaScript events regarding the pagination of blog posts in the frontend. Our custom triggers always start with `z7_blog` to make them distinguishible from potential other custom events. Currently the available events are

| Event name | What it does |
| ------ | ------ |
| `z7_blog:ajax:statechange` | Triggered whenever the state of the ajax request changes |
| `z7_blog:ajax:send` | Triggered when the ajax call is started (for example the "load more" buttons has been pressed) |
| `z7_blog:ajax:done` | Triggered when the ajax call has come to an end, no matter if successful or not |
| `z7_blog:ajax:success` | Triggered when the ajax call successfully returned an answer (state 200) |
| `z7_blog:ajax:error` | Triggered when the ajax call returned an error |
| `z7_blog:addToList:complete` | Triggered after the new list items have been added to the DOM |

An example implementation could look like this:
```js
document.addEventListener('z7_blog:addToList:complete', e => {
  console.log('Do something after the new blog posts have been added to the DOM');
});
```

:point_up: **Tip:** The `z7_blog:addToList:complete` event trigger also hands over various variables you can nicely use to modify them more. For example all new items that just have been added to the list will be handed over.

### SEO config

If filters are used as GET parameters, it is often advised to exclude them from being crawled. Especially when having a mulitselection of tags and topis, there can quickly be thousands of combinations being crawled.

Example robots.txt:

```
Disallow: *tx_z7blog_list%5Btopics%5D=*s
Disallow: *tx_z7blog_list%5Btags%5D=*
```

### Structured data

Every post automatically gets structured data. If you want to expand these, you can edit it via TypoScript.

Example:

```typo3_typoscript
plugin.tx_z7blog.settings.post.structuredData {

    # Create new attribute "publisher"
    publisher {

        # Define type "Organisation"
        typeOrganization {

            # … and so on
            name = zeroseven design studios GmbH
            logo.typeImageObject {
                url = https://www.zeroseven.de/resources/logo.png
                width = 365
                height = 28
            }
        }
    }
}
```

To create a new `@type`, you can prefix it with the corresponding `type` in the configuration.

### Custom conditions for fluid templates

Our blog offers custom conditions to work with in your fluid templates. It's best shown by providing a simple example. Let's say you want to add some extra content to all headers, but only on blog posts:

```HTML
<blog:condition.isPost>
    <span>It's a post</span>
</blog:condition.isPost>
```

You can also integrate it's usage in the standard `ifViewHelper` to achieve even more flexibility:

```HTML
<f:if condition="{media} && {blog:condition.isPost()}">
    <f:then>
        <span>It's a post with an image</span>
    <f:then>
    <f:else>
        <span>It's a "normal" page or the image is missing</span>
    <f:else>
</f:if>
```

Check out all custom conditions the z7_blog has to offer in the `Classes/ViewHelpers/Conditions` directory.

## :construction: Todo:

* Upgrades from various TYPO3 Blog extensions could be run via an upgrade wizard
* Integration of PSR14-Events for controller, repository, structured data, ...
