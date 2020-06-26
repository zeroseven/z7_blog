(function () {

  const removeChilds = element => {

    let child;
    while (child = element.firstElementChild) {
      element.removeChild(child);
    }

    return element;
  };

  const appendChilds = (sourceElement, targetElement) => {

    let child;
    while (child = sourceElement.firstElementChild) {
      targetElement.append(child);
    }

    return targetElement;
  };

  const addToList = (listSelector, controlSelector, loadingText, button, e) => {

    const event = e || window.event;

    const elements = {
      button: button || event.target,
      list: document.querySelector(listSelector),
      control: document.querySelector(controlSelector)
    };

    // Replace url
    window.history.replaceState(null, null, elements.button.href);

    // Stop default behaviour of the event
    if(typeof event !== 'undefined') {
      event.preventDefault();
    }

    // Disable the target to prevent a second click
    elements.button.href = 'javascript:void(0)';
    elements.button.removeAttribute('onclick');

    // Change the layout of the target
    elements.button.style.cursor = 'no-drop';

    if(loadingText) {
      elements.button.innerText = loadingText;
    }

    // Add data attributes
    Object.keys(elements).forEach(key => elements[key].dataset.loading = 'true');

    // Load content
    Zeroseven.Blog.loadContents(elements.button.dataset.href, (contents, status) => {
      if (status < 400) {

        // Remove loading classes
        Object.keys(elements).forEach(key => delete elements[key].dataset.loading);

        // Append items
        appendChilds(contents[listSelector], elements.list);

        // Replace content of the control area
        appendChilds(contents[controlSelector], removeChilds(elements.control), true);

      } else {
        if (confirm('The requested site could not be loaded:\n' + status + ').\n\nDo you want to try again?')) {
          window.location.href = elements.button.href;
        }
      }
    }, listSelector, controlSelector);
  };

  // Add to namespace
  Zeroseven.Blog.register('addToList', addToList);
})();
