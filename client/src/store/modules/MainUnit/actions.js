import api from "@/plugins/api";

const prefix = "/main-unit";

export const all = ({ commit }, queries) => {
	api("get", prefix + queries, (err, res) => {
		if (err) return;
		commit("all", res);
	});
};

export const options = ({ commit }) => {
	return api("get", `${prefix}/options`, (err, res) => {
		if (err) return;
		commit("options", res);
	});
};

export const create = ({ dispatch }, item) => {
	return api("post", `${prefix}/create`, item, (err) => {
		if (err) return;
		dispatch("all");
	});
};

export const update = ({ dispatch }, item) => {
	return api("put", `${prefix}/${item.id}`, item, (err) => {
		if (err) return;
		dispatch("all");
	});
};

export const remove = ({ commit }, item) => {
	api("post", `${prefix}/${item.id}`, item, (err, res) => {
		if (err) return;
		commit("remove", res);
	});
};

export const createSubUnit = ({ dispatch }, item) => {
	return api("post", "/sub-unit/create", item, (err) => {
		if (err) return;
		dispatch("all");
	});
};

export const updateSubUnit = ({ dispatch }, item) => {
	return api("put", `/sub-unit/${item.id}`, item, (err) => {
		if (err) return;
		dispatch("all");
	});
};

export const removeSubUnit = ({ commit }, item) => {
	api("post", `/sub-unit/${item.id}`, item, (err) => {
		if (err) return;
		commit("removeSubUnit", item);
	});
};
