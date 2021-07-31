export const all = ({ commit }, queries) => {
	let response = "This where is api action go";
	commit("all", response);
};

export const one = ({ commit }, salesId) => {
	let response = "This where is api action go";
	console.log(salesId);
	commit("one", response);
};

export const create = ({ commit }, sales) => {
	let response = "This where is api action go";
	console.log(sales);
	commit("create", response);
};

export const update = ({ commit }, sales) => {
	let response = "This where is api action go";
	console.log(sales);
	commit("update", response);
};

export const moveToTrash = ({ commit }, salesId) => {
	let response = "This where is api action go";
	console.log(salesId);
	commit("moveToTrash", response);
};

export const remove = ({ commit }, salesId) => {
	let response = "This where is api action go";
	console.log(salesId);
	commit("remove", response);
};
