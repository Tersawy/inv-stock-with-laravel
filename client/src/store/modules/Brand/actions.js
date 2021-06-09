import api from "@/plugins/api";

const prefix = "/brand";

export const all = ({ commit }) => {
	api("get", `${prefix}`, (err, res) => {
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

export const one = ({ commit }, itemId) => {
	return api("get", `${prefix}/${itemId}`, (err, res) => {
		if (err) return;
		commit("one", res);
	});
};

export const create = ({ commit }, item) => {
	return api("post", `${prefix}/create`, item, (err, res) => {
		if (err) return;
		commit("create", res);
	});
};

export const update = ({ commit }, item) => {
	item.set("_method", "put");
	return api("post", `${prefix}/${item.get("id")}`, item, (err, res) => {
		if (err) return;
		commit("update", res);
	});
};

export const moveToTrash = ({ commit }, item) => {
	api("post", `${prefix}/${item.id}/trash`, item, (err, res) => {
		if (err) return;
		commit("moveToTrash", res);
	});
};

export const trashed = ({ commit }, item) => {
	api("get", `${prefix}/trashed`, item, (err, res) => {
		if (err) return;
		commit("trashed", res);
	});
};

export const restore = ({ commit }, item) => {
	api("post", `${prefix}/${item.id}/restore`, item, (err, res) => {
		if (err) return;
		commit("restore", res);
	});
};

export const remove = ({ commit }, item) => {
	api("post", `${prefix}/${item.id}`, item, (err, res) => {
		if (err) return;
		commit("remove", res);
	});
};
