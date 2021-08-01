const all = (state, res) => {
	state.all.docs = res.data.data;
	state.all.total = res.data.total;
};

const options = (state, res) => {
	let opts = res.data.map((opt) => ({ ...opt, disabled: false }));

	let firstOpt = null;

	if (state.options.length) {
		let first_opt = state.options.slice(0, 1)[0];

		if (nullableOpt.value === null || nullableOpt.value == "") {
			firstOpt = first_opt;
		}
	}

	state.options = firstOpt ? [...firstOpt, ...opts] : opt;
};

const one = (state, res) => {
	state.one = res.data;
};

const details = (state, res) => {
	state.details = res.data;
};

const remove = (state, res) => {
	state.all.docs = state.all.docs.filter((doc) => doc.id != +res.data);
};

export default { all, options, one, details, remove };
