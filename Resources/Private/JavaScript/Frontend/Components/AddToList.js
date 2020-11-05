Zeroseven.Blog.Utility.register('addToList', (listSelector, controlSelector, loadingText, button, e) => {

  const event = e || window.event;

  const elements = {
    button: button || event.target,
    list: document.querySelector(listSelector),
    control: document.querySelector(controlSelector)
  };

  // Replace url
  window.history.replaceState(null, null, elements.button.href);

  // Stop default behaviour of the event
  if (typeof event !== 'undefined') {
    event.preventDefault();
  }

  // Disable the target to prevent a second click
  elements.button.href = 'javascript:void(0)';
  elements.button.removeAttribute('onclick');

  // Change the layout of the target
  elements.button.style.cursor = 'no-drop';

  if (loadingText) {
    elements.button.innerText = loadingText;
  }

  // Add data attributes
  Object.keys(elements).forEach(key => elements[key].dataset.loading = 'true');

  // Load content
  Zeroseven.Blog.Utility.loadContents(elements.button.dataset.href, null, (contents, status) => {
    if (status < 400) {

      // Get the number of links
      const linkLength = elements.list.getElementsByTagName('a').length;

      // Remove loading classes
      Object.keys(elements).forEach(key => delete elements[key].dataset.loading);

      // Append items
      const newItems = Zeroseven.Blog.Utility.appendChilds(contents[listSelector], elements.list);

      // Replace content of the control area
      const newControls = Zeroseven.Blog.Utility.appendChilds(contents[controlSelector], Zeroseven.Blog.Utility.removeChilds(elements.control));

      // Focus the first link of new results
      elements.list.getElementsByTagName('a')[linkLength].focus();

      // Trigger event
      Zeroseven.Blog.Utility.trigger('addToList:complete', {elements: elements, items: newItems, controls: newControls});

    } else {
      if (confirm('The requested site could not be loaded:\n' + status + ').\n\nDo you want to try again?')) {
        window.location.href = elements.button.href;
      }
    }
  }, listSelector, controlSelector);
});
