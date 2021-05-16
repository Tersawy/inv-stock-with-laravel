export const all = ({ commit }) => {
	let response = "This where is api action go";
	commit("all", response);
};

export const one = ({ commit }, supplierId) => {
	let response = "This where is api action go";
	console.log(supplierId);
	commit("one", response);
};

export const create = ({ commit }, supplier) => {
	let response = "This where is api action go";
	console.log(supplier);
	commit("create", response);
};

export const update = ({ commit }, supplier) => {
	let response = "This where is api action go";
	console.log(supplier);
	commit("update", response);
};

export const moveToTrash = ({ commit }, supplierId) => {
	let response = "This where is api action go";
	console.log(supplierId);
	commit("moveToTrash", response);
};

export const remove = ({ commit }, supplierId) => {
	let response = "This where is api action go";
	console.log(supplierId);
	commit("remove", response);
};
