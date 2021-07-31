import api from "@/plugins/api";

const prefix = "/supplier";

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

export const one = ({ commit }, supplierId) => {
	return api("get", `${prefix}/${supplierId}`, (err, res) => {
		if (err) return;
		commit("one", res);
	});
};

export const create = ({ commit }, supplier) => {
	api("post", `${prefix}/create`, supplier, (err, res) => {
		if (err) return;
		commit("create", res);
	});
};

export const update = ({ commit }, supplier) => {
	api("put", `${prefix}/${supplier.id}`, supplier, (err, res) => {
		if (err) return;
		commit("update", res);
	});
};

export const moveToTrash = ({ commit }, supplier) => {
	api("post", `${prefix}/${supplier.id}/trash`, supplier, (err, res) => {
		if (err) return;
		commit("moveToTrash", res);
	});
};

export const trashed = ({ commit }, supplier) => {
	api("get", `${prefix}/trashed`, supplier, (err, res) => {
		if (err) return;
		commit("trashed", res);
	});
};

export const restore = ({ commit }, supplier) => {
	api("post", `${prefix}/${supplier.id}/restore`, supplier, (err, res) => {
		if (err) return;
		commit("restore", res);
	});
};

export const remove = ({ commit }, supplier) => {
	api("post", `${prefix}/${supplier.id}`, supplier, (err, res) => {
		if (err) return;
		commit("remove", res);
	});
};
