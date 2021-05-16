export const all = ({ commit }) => {
	let response = "This where is api action go";
	commit("all", response);
};

export const one = ({ commit }, expenseTypeId) => {
	let response = "This where is api action go";
	console.log(expenseTypeId);
	commit("one", response);
};

export const create = ({ commit }, expenseType) => {
	let response = "This where is api action go";
	console.log(expenseType);
	commit("create", response);
};

export const update = ({ commit }, expenseType) => {
	let response = "This where is api action go";
	console.log(expenseType);
	commit("update", response);
};

export const moveToTrash = ({ commit }, expenseTypeId) => {
	let response = "This where is api action go";
	console.log(expenseTypeId);
	commit("moveToTrash", response);
};

export const remove = ({ commit }, expenseTypeId) => {
	let response = "This where is api action go";
	console.log(expenseTypeId);
	commit("remove", response);
};
