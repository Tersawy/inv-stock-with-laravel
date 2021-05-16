import uiUitils from "../../components/uiUitils";

export const setSuccess = (state, message) => uiUitils.showToast(message, "danger");

export const setErrors = (state, { message, errors }) => {
	state.errors = errors;
	uiUitils.showToast(message, "danger");
};

export const removeErrors = (state) => (state.errors = {});
