// import uiUitils from "../../components/uiUitils";

export const setSuccess = (state, msg) => {
	state.successMsg = msg;
	state.successDismissSecs = 5;
};

export const removeSuccess = (state) => {
	state.successMsg = null;
	state.successDismissSecs = 0;
};

export const setError = (state, msg) => {
	state.errorMsg = msg;
	state.errorDismissSecs = 5;
};

export const removeError = (state) => {
	state.errorDismissSecs = 0;
	state.errorMsg = null;
};

export const setErrors = (state, { message, errors }) => {
	state.errorMsg = message;
	state.errorDismissSecs = 5;
	state.errors = errors;
	console.log(message);
	// uiUitils.showToast(message, "text");
};

export const removeErrors = (state) => {
	state.errorMsg = null;
	state.errorDismissSecs = 0;
	state.errors = {};
};
