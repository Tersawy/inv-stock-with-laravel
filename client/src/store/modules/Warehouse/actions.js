import api from "@/plugins/api";

const prefix = "/warehouse";

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

export const one = ({ commit }, warehouseId) => {
	return api("get", `${prefix}/${warehouseId}`, (err, res) => {
		if (err) return;
		commit("one", res);
	});
};

export const create = ({ commit }, warehouse) => {
	api("post", `${prefix}/create`, warehouse, (err, res) => {
		if (err) return;
		commit("create", res);
	});
};

export const update = ({ commit }, warehouse) => {
	api("put", `${prefix}/${warehouse.id}`, warehouse, (err, res) => {
		if (err) return;
		commit("update", res);
	});
};

export const moveToTrash = ({ commit }, warehouse) => {
	api("post", `${prefix}/${warehouse.id}/trash`, warehouse, (err, res) => {
		if (err) return;
		commit("moveToTrash", res);
	});
};

export const trashed = ({ commit }, warehouse) => {
	api("get", `${prefix}/trashed`, warehouse, (err, res) => {
		if (err) return;
		commit("trashed", res);
	});
};

export const restore = ({ commit }, warehouse) => {
	api("post", `${prefix}/${warehouse.id}/restore`, warehouse, (err, res) => {
		if (err) return;
		commit("restore", res);
	});
};

export const remove = ({ commit }, warehouse) => {
	api("post", `${prefix}/${warehouse.id}`, warehouse, (err, res) => {
		if (err) return;
		commit("remove", res);
	});
};
