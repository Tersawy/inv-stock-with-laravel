import uiUtils from "../../components/uiUtils";

export const setSuccess = (state, message) => uiUtils.showToast(message, "danger");

export const setErrors = (state, { message, errors }) => {
	state.errors = errors;
	uiUtils.showToast(message, "danger");
};

export const removeErrors = (state) => (state.errors = {});
