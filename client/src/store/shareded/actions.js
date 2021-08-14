import api from "@/plugins/api";

const all = ({ commit, state }, queries = "") => {
	api("get", state.prefix + queries, (err, res) => {
		if (err) return;
		commit("all", res);
	});
};

const options = ({ commit, state }) => {
	return api("get", `${state.prefix}/options`, (err, res) => {
		if (err) return;
		commit("options", res);
	});
};

const one = ({ commit, state }, itemId) => {
	return api("get", `${state.prefix}/${itemId}`, (err, res) => {
		if (err) return;
		commit("one", res);
	});
};

const create = ({ state, dispatch }, item) => {
	return api("post", `${state.prefix}/create`, item, (err, res) => {
		if (err) return Promise.reject(err);
		dispatch("all");
		return Promise.resolve(res);
	});
};

const update = ({ state, dispatch }, item) => {
	return api("put", `${state.prefix}/${item.id}`, item, (err, res) => {
		if (err) return Promise.reject(err);
		dispatch("all");
		return Promise.resolve(res);
	});
};

const moveToTrash = ({ commit, state }, item) => {
	return api("post", `${state.prefix}/${item.id}/trash`, item, (err, res) => {
		if (err) return Promise.reject(err);
		commit("remove", item.id);
		return Promise.resolve(res);
	});
};

const trashed = ({ commit, state }, item) => {
	api("get", `${state.prefix}/trashed`, item, (err, res) => {
		if (err) return;
		commit("all", res);
	});
};

const restore = ({ commit, state }, item) => {
	return api("post", `${state.prefix}/${item.id}/restore`, item, (err, res) => {
		if (err) return Promise.reject(err);
		commit("remove", item.id);
		return Promise.resolve(res);
	});
};

const remove = ({ commit, state }, item) => {
	return api("post", `${state.prefix}/${item.id}`, item, (err, res) => {
		if (err) return Promise.reject(err);
		commit("remove", item.id);
		return Promise.resolve(res);
	});
};

export default { all, options, one, create, update, moveToTrash, trashed, restore, remove };
