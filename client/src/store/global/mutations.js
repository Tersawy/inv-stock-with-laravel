import { showToast } from "@/components/ui/utils";

export const setSuccess = (_state, message) => showToast(message);

export const setErrors = (state, { message, errors }) => {
	state.errors = errors;
	if (message) showToast(message, "danger");
};

export const setError = (_state, message) => showToast(message, "danger");

export const setBreads = (state, breads) => (state.breads = breads);
