export const all = (state, res) => {
	state.all.docs = res.data;
};

export const options = (state, res) => {
	let opts = res.data.map((opt) => ({ ...opt, disabled: false }));
	state.options = [{ text: "Choose Product Unit", value: null, disabled: true }, ...opts];
};

export const remove = (state, res) => {
	state.all.docs = state.all.docs.filter((doc) => doc.id != +res.data);
	state.options = state.options.filter((doc) => doc.id != +res.data);
};

export const removeSubUnit = (state, subUnit) => {
	let mainUnit = state.all.docs.find((doc) => doc.id == +subUnit.main_unit);
	if (mainUnit) {
		mainUnit.sub_units = mainUnit.sub_units.filter((doc) => doc.id != +subUnit.id);
	}
};
