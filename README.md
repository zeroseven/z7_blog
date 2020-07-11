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
