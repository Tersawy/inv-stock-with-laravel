import api from "@/plugins/api";

const all = ({ commit, state }, queries = "") => {
	api("get", state.prefix + queries, (err, res) => {
		if (err) return commit("setErrors", err);
		commit("all", res);
	});
};

const options = ({ commit, state }) => {
	return api("get", `${state.prefix}/options`, (err, res) => {
		if (err) return commit("setErrors", err);
		commit("options", res);
	});
};

const edit = ({ commit, state }, itemId) => {
	return api("get", `${state.prefix}/${itemId}/edit`, (err, res) => {
		if (err) return commit("setErrors", err);
		commit("one", res);
	});
};

const one = ({ commit, state }, itemId) => {
	return api("get", `${state.prefix}/${itemId}`, (err, res) => {
		if (err) return commit("setErrors", err);
		commit("one", res);
	});
};

const create = ({ state, commit, dispatch }, item) => {
	return api("post", `${state.prefix}/create`, item, (err, res) => {
		if (err) {
			commit("setErrors", err);
			return Promise.reject(err);
		}
		dispatch("all");
		return Promise.resolve(res);
	});
};

const update = ({ state, commit, dispatch }, item) => {
	return api("put", `${state.prefix}/${item.id}`, item, (err, res) => {
		if (err) {
			commit("setErrors", err);
			return Promise.reject(err);
		}
		dispatch("all");
		return Promise.resolve(res);
	});
};

const moveToTrash = ({ commit, state }, item) => {
	return api("post", `${state.prefix}/${item.id}/trash`, item, (err, res) => {
		if (err) {
			commit("setErrors", err);
			return Promise.reject(err);
		}
		commit("remove", item.id);
		return Promise.resolve(res);
	});
};

const trashed = ({ commit, state }, item) => {
	api("get", `${state.prefix}/trashed`, item, (err, res) => {
		if (err) return commit("setErrors", err);
		commit("all", res);
	});
};

const restore = ({ commit, state }, item) => {
	return api("post", `${state.prefix}/${item.id}/restore`, item, (err, res) => {
		if (err) {
			commit("setErrors", err);
			return Promise.reject(err);
		}
		commit("remove", item.id);
		return Promise.resolve(res);
	});
};

const remove = ({ commit, state }, item) => {
	return api("post", `${state.prefix}/${item.id}`, item, (err, res) => {
		if (err) {
			commit("setErrors", err);
			return Promise.reject(err);
		}
		commit("remove", item.id);
		return Promise.resolve(res);
	});
};

export default { all, options, edit, one, create, update, moveToTrash, trashed, restore, remove };
