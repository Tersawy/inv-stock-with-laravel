import { showToast } from "@/components/ui/utils";

export const setSuccess = (state, message) => showToast(message);

export const setErrors = (state, { message, errors }) => {
	state.errors = errors;
	showToast(message, "danger");
};

export const removeErrors = (state) => (state.errors = {});

export const removeError = (state, field) => (state.errors[field] = null);
