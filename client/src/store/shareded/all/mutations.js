import { showToast } from "@/components/ui/utils";

export default {
	all(state, res) {
		state.all.docs = res.data.data;
		state.all.total = res.data.total;
	},

	options(state, res) {
		let opts = res.data.map((opt) => ({ ...opt, disabled: false }));

		let firstOpt = null;

		if (state.options.length) {
			let first_opt = state.options.slice(0, 1)[0];

			if (first_opt.value === null || first_opt.value == "") {
				firstOpt = first_opt;
			}
		}

		state.options = firstOpt ? [firstOpt, ...opts] : opts;
	},

	one(state, res) {
		state.one = res.data;
	},

	remove(state, res) {
		state.all.docs = state.all.docs.filter((doc) => doc.id != +res.data);
	},

	setError(state, { field, msg }) {
		state.errors = { ...state.errors, [field]: [msg] };
	},

	removeError(state, field) {
		state.errors = Object.keys(state.errors)
			.filter((f) => f != field)
			.map((f) => ({ [f]: state.errors[f] }));
	},

	setErrors(state, { message, errors }) {
		state.errors = errors;
		if (message) showToast(message, "danger");
	},

	removeErrors(state) {
		state.errors = {};
	}
};
