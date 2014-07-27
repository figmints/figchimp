// Polyfill on event listener
Element.prototype.on = function (type, fn) {
  if (this.attachEvent) {
    this['e'+type+fn] = fn;
    this[type+fn] = function () {
      this['e'+type+fn]( window.event );
    };
    this.attachEvent('on'+type, this[type+fn]);
  } else {
    this.addEventListener(type, fn, false);
  }
};

(function($, window, document, undefined) {

  // Figchimp Form
  var form  = document.querySelector('#figchimp');
  // Email input
  var email = form.querySelector('.email');

  // Submit handler
  function signupForNewsletter(e) {
    e.preventDefault();
    $.ajax({
      url: ADMIN_URL + "admin-ajax.php",
      type: 'POST',
      data: {
        action: 'figchimp_subscribe',
        email: email.value
      },
      dataType: 'JSON'
    })
    .done(function(response) {
      var $email     = $(form).find('.email');
      var emailRegex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

      if (response.error || emailRegex.test(email.value) === false) {
        $('.figchimp-message').remove();
        $email.addClass('error');
        $email.next().after('<div class="figchimp-message">Your email is incorrect.</div>')
      } else {
        $email.removeClass('error');
        $('.figchimp-message').remove();
        $email.next().after('<div class="figchimp-message">Subscribed - look for the confirmation email!</div>');
      };
      setTimeout(function() {
        $('.figchimp-message').remove();
      }, 2750)
    })
    .fail(function(response) {
      console.log(response);
    });
  };

  form.on('submit', signupForNewsletter);

})(jQuery, window, document);