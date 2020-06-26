Zeroseven.Blog.register('trigger', (name, parameter) => {
  let event;

  if ('function' === typeof window.CustomEvent) {
    event = new CustomEvent('z7_blog:' + name, {detail: parameter});
  } else {
    event = document.createEvent('CustomEvent');
    event.initCustomEvent('z7_blog:' + name, true, true, parameter);
  }

  document.dispatchEvent(event);

  return parameter;
});
