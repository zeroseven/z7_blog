Zeroseven.Blog.register('loadContents', (url, callback, ...selectors) => {

  let contents = {};
  let request = new XMLHttpRequest();
  let trigger = Zeroseven.Blog.trigger;

  trigger('ajax:send', {url: url, selectors: selectors});

  request.onreadystatechange = () => {

    // Parse document
    if (request.readyState === 4) {

      trigger('ajax:ready', {url: url, selectors: selectors, request:request});

      if (request.status === 200) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(request.responseText, 'text/html');

        // Loop the content selectors thought new page
        selectors.forEach(selector => {
          contents[selector] = doc.querySelector(selector);
        });

        trigger('ajax:complete', {url: url, selectors: selectors, request:request, contents: contents});
      }

      // Run callback action
      if(typeof callback === 'function') {
        callback(contents, request.status);
      }
    } else {
      trigger('ajax:error', {url: url, selectors: selectors, request:request, callback:callback});
    }
  };

  // Start request
  request.open('Get', url);
  request.send();
});
