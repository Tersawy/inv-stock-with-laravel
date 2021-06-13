export const all = (state, res) => {
	state.all.docs = res.data;
};

export const one = (state, res) => {
	state.one = res.data;
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
