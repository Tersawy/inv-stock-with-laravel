import { showToast } from "@/components/ui/utils";

export const setSuccess = (state, message) => showToast(message);

export const setErrors = (state, { message, errors }) => {
	state.errors = errors;
	showToast(message, "danger");
};

export const removeErrors = (state) => (state.errors = {});

export const setError = (state, { field, msg }) => (state.errors[field] = msg);

export const removeError = (state, field) => (state.errors[field] = null);

export const setBreads = (state, breads) => (state.breads = breads);
