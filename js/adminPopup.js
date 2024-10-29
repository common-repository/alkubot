/* global jQuery */

jQuery(document).ready(() => {
	initAlkubotPopup();
});

const initAlkubotPopup = () => {
	setAlkubotPopupContent();

	closeAlkubotPopupHandler();
};

const closeAlkubotPopupHandler = () => {
	jQuery("#alkubot-popup-overlay").click(() => {
		hideAlkubotPopup();
	});
};

const setAlkubotPopupContentById = (id) => {
	const content = jQuery("#" + id).html();

	setAlkubotPopupContent(content);
};

const setAlkubotPopupContent = (content = false) => {
	if (!content) {
		content = jQuery("#alkubot-success-popup-content").html();
	}

	jQuery("#alkubot-popup-container").html(content);
};

const showAlkubotErrorPopup = () => {
	const content = jQuery("#alkubot-error-popup-content").html();
	setAlkubotPopupContent(content);
	showAlkubotPopup();
};

const showAlkubotSuccessPopup = () => {
	const content = jQuery("#alkubot-success-popup-content").html();
	setAlkubotPopupContent(content);
	showAlkubotPopup();
};

const showAlkubotPopup = () => {
	jQuery("#alkubot-popup").removeClass("alk-d-none");
};

const hideAlkubotPopup = () => {
	jQuery("#alkubot-popup").addClass("alk-d-none");
};
