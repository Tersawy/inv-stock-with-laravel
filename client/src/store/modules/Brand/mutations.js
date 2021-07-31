export const all = (state, res) => {
	state.all.docs = res.data;
};

export const options = (state, res) => {
	let opts = res.data.map((opt) => ({ ...opt, disabled: false }));
	state.options = [{ text: "Choose Brand", value: null, disabled: true }, ...opts];
};

export const one = (state, res) => {
	state.one = res.data;
};

export const create = (state, res) => {
	state.all.docs = [res.data, ...state.all.docs];
};

export const update = (state, res) => {
	state.all.docs = state.all.docs.map((doc) => {
		if (+doc.id == +res.data.id) {
			for (let d in doc) {
				doc[d] = res.data[d];
			}
		}
		return doc;
	});
	state.options = state.options.map((option) => {
		if (+option.id == +res.data.id) {
			for (let d in option) {
				option[d] = res.data[d];
			}
		}
		return option;
	});
};

export const moveToTrash = (state, res) => {
	state.all.docs = state.all.docs.filter((doc) => doc.id != +res.data);
};

export const trashed = (state, res) => {
	state.all.docs = res.data;
};

export const restore = (state, res) => {
	state.all.docs = state.all.docs.filter((doc) => doc.id != +res.data);
};

export const remove = (state, res) => {
	state.all.docs = state.all.docs.filter((doc) => doc.id != +res.data);
};
