(function () {

  // Register functions and variables
  const register = (key, value) => {
    window.Zeroseven = window.Zeroseven || {};
    window.Zeroseven.Blog = window.Zeroseven.Blog || {};
    window.Zeroseven.Blog[key] = value;

    return value;
  };

  // Register registration itself
  register('register', register);
})();
