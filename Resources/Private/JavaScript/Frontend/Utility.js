(function () {

  class Utility {

    /**
     * Register value in blog namespace
     *
     * @param key
     * @param value
     * @returns {*}
     */
    static register(key, value) {
      window.Zeroseven = window.Zeroseven || {};
      window.Zeroseven.Blog = window.Zeroseven.Blog || {};
      window.Zeroseven.Blog[key] = value;

      return value;
    }

    /**
     * Clear's the content of a node
     *
     * @param wrapper
     * @returns {*}
     */
    static removeChilds(wrapper) {
      let firstChild;
      while (firstChild = wrapper.firstElementChild) {
        wrapper.removeChild(firstChild);
      }

      return wrapper;
    }

    /**
     * Clear's the content of a node
     *
     * @param sourceElement
     * @param targetElement
     * @returns {*}
     */
    static appendChilds(sourceElement, targetElement) {
      let child;
      while (child = sourceElement.firstElementChild) {
        targetElement.append(child);
      }

      return targetElement;
    }

    /**
     * Add a custom eventListener to the "document"
     *
     * @param name
     * @param parameter
     * @returns parameter
     **/
    static trigger(name, parameter) {
      let event;

      if ('function' === typeof window.CustomEvent) {
        event = new CustomEvent('z7_blog:' + name, {detail: parameter});
      } else {
        event = document.createEvent('CustomEvent');
        event.initCustomEvent('z7_blog:' + name, true, true, parameter);
      }

      document.dispatchEvent(event);

      return parameter;
    }

    /**
     * Load HTML contents
     *
     * @param url
     * @param postData
     * @param callback
     * @param selectors
     * @returns function
     */
    static loadContents(url, postData, callback, ...selectors) {

      let contents = {};
      let request = new XMLHttpRequest();

      this.trigger('ajax:send', {url: url, selectors: selectors});

      request.onreadystatechange = () => {

        // Parse document
        if (request.readyState === 4) {

          this.trigger('ajax:ready', {url: url, selectors: selectors, request: request});

          if (request.status === 200) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(request.responseText, 'text/html');

            // Loop the content selectors thought new page
            selectors.forEach(selector => {
              contents[selector] = doc.querySelector(selector);
            });

            this.trigger('ajax:complete', {url: url, selectors: selectors, request: request, contents: contents});
          }

          // Run callback action
          if (typeof callback === 'function') {
            callback(contents, request.status);
          }
        } else {
          this.trigger('ajax:error', {url: url, selectors: selectors, request: request, callback: callback});
        }
      };

      // Determine post data
      let postDataString = '';
      if(postData && typeof postData === 'object') {
        Object.keys(postData).forEach(key => {
          postDataString += (postDataString ? '&' : '') + key + (postData[key] ? '=' + encodeURI(postData[key]) : '');
        });
      }

      // Start request
      request.open('POST', url, true);
      request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
      request.send(postDataString);
    }
  }

  // Register utility
  Utility.register('Utility', Utility);
})();
