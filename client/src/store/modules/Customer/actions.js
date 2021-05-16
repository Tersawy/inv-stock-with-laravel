export const all = ({ commit }) => {
	let response = "This where is api action go";
	commit("all", response);
};

export const one = ({ commit }, customerId) => {
	let response = "This where is api action go";
	console.log(customerId);
	commit("one", response);
};

export const create = ({ commit }, customer) => {
	let response = "This where is api action go";
	console.log(customer);
	commit("create", response);
};

export const update = ({ commit }, customer) => {
	let response = "This where is api action go";
	console.log(customer);
	commit("update", response);
};

export const moveToTrash = ({ commit }, customerId) => {
	let response = "This where is api action go";
	console.log(customerId);
	commit("moveToTrash", response);
};

export const remove = ({ commit }, customerId) => {
	let response = "This where is api action go";
	console.log(customerId);
	commit("remove", response);
};
