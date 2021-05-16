export const all = ({ commit }) => {
	let response = "This where is api action go";
	commit("all", response);
};

export const one = ({ commit }, categoryId) => {
	let response = "This where is api action go";
	console.log(categoryId);
	commit("one", response);
};

export const create = ({ commit }, category) => {
	let response = "This where is api action go";
	console.log(category);
	commit("create", response);
};

export const update = ({ commit }, category) => {
	let response = "This where is api action go";
	console.log(category);
	commit("update", response);
};

export const moveToTrash = ({ commit }, categoryId) => {
	let response = "This where is api action go";
	console.log(categoryId);
	commit("moveToTrash", response);
};

export const remove = ({ commit }, categoryId) => {
	let response = "This where is api action go";
	console.log(categoryId);
	commit("remove", response);
};
