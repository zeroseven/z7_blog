# z7_blog

## Add details of the blogpost to the template

If you need information about the post on each detail page, there's no need to maintain each one individually. Use the following TypoScript to add blog content to each post at a place of your liking.

```typo3_typoscript
page.1593548755 = USER
page.1593548755 {
    userFunc = Zeroseven\Z7Blog\Utility\InfoRenderUtility->renderUserFunc
    file = EXT:z7_blog/Resources/Private/Partials/Post/Info/Summary.html
}
```

Or append the post data like a content element to the page

```typo3_typoscript
page.1593548755 < lib.contentElement
page.1593548755 {
    templateName = Generic

    20 = USER
    20 {
        userFunc = Zeroseven\Z7Blog\Utility\InfoRenderUtility->renderUserFunc
        file = EXT:z7_blog/Resources/Private/Partials/Post/Info/Related.html
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
