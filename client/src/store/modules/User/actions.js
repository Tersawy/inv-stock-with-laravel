export const all = ({ commit }, queries) => {
	let response = "This where is api action go";
	commit("all", response);
};

export const one = ({ commit }, userId) => {
	let response = "This where is api action go";
	console.log(userId);
	commit("one", response);
};

export const create = ({ commit }, user) => {
	let response = "This where is api action go";
	console.log(user);
	commit("create", response);
};

export const update = ({ commit }, user) => {
	let response = "This where is api action go";
	console.log(user);
	commit("update", response);
};

export const moveToTrash = ({ commit }, userId) => {
	let response = "This where is api action go";
	console.log(userId);
	commit("moveToTrash", response);
};

export const remove = ({ commit }, userId) => {
	let response = "This where is api action go";
	console.log(userId);
	commit("remove", response);
};
