/* global jQuery, alkubot */

jQuery(document)
.ready(() => {
  processAjaxForm();
});

const processAjaxForm = () => {
  jQuery(".ajaxForm")
  .submit((e) => {
    e.preventDefault();

    const form = jQuery(e.currentTarget);
    const data = form.serializeArray();
    data.push({
      name: "security",
      value: alkubot.security
    });

    const options = {
      form: form,
      url: alkubot.ajaxUrl,
      data: data
    };

    processAJAXRequest(options);
  });
};

const processAJAXRequest = (options) => {
  jQuery.ajax({
    method: "POST",
    url: options.url,
    data: options.data,
    beforeSend: () => {
      showAlkubotLoading();
    }
  })
  .done(() => {
    hideAlkubotLoading();
    showAlkubotSuccessPopup();
  })
  .fail(() => {
    hideAlkubotLoading();
    showAlkubotErrorPopup();
  });
};

const showAlkubotLoading = () => {
  jQuery('#loading-overlay').show();
};

const hideAlkubotLoading = () => {
  jQuery('#loading-overlay').hide();
};
