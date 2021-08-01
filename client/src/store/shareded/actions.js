import api from "@/plugins/api";

const all = ({ commit, state }, queries) => {
	api("get", state.prefix + queries || "", (err, res) => {
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

const details = ({ commit, state }, itemId) => {
	return api("get", `${state.prefix}/details/${itemId}`, (err, res) => {
		if (err) return;
		commit("details", res);
	});
};

const create = ({ state, dispatch }, item) => {
	return api(
		"post",
		`${state.prefix}/create`,
		item,
		(err) => {
			if (err) return;
			dispatch("all");
		},
		true
	);
};

const update = ({ state, dispatch }, item) => {
	item.set("_method", "put");
	return api("post", `${state.prefix}/${item.get("id")}`, item, (err) => {
		if (err) return;
		dispatch("all");
	});
};

const moveToTrash = ({ commit, state }, item) => {
	api("post", `${state.prefix}/${item.id}/trash`, item, (err) => {
		if (err) return;
		commit("remove", item.id);
	});
};

const trashed = ({ commit, state }, item) => {
	api("get", `${state.prefix}/trashed`, item, (err, res) => {
		if (err) return;
		commit("all", res);
	});
};

const restore = ({ commit, state }, item) => {
	api("post", `${state.prefix}/${item.id}/restore`, item, (err) => {
		if (err) return;
		commit("remove", item.id);
	});
};

const remove = ({ commit, state }, item) => {
	api("post", `${state.prefix}/${item.id}`, item, (err) => {
		if (err) return;
		commit("remove", item.id);
	});
};

export default { all, options, one, details, create, update, moveToTrash, trashed, restore, remove };
