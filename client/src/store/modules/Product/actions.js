import api from "@/plugins/api";

export default {
	details({ commit, state }, item) {
		return api("post", `${state.prefix}/details`, item, (err, res) => {
			if (err) return;
			commit("details", res);
		});
	},

	create({ state, dispatch }, item) {
		return api(
			"post",
			`${state.prefix}/create`,
			item,
			(err, res) => {
				if (err) return Promise.reject(err);
				dispatch("all");
				return Promise.resolve(res);
			},
			true
		);
	},

	update({ state, dispatch }, item) {
		item.set("_method", "put");
		return api("post", `${state.prefix}/${item.get("id")}`, item, (err, res) => {
			if (err) return Promise.reject(err);
			dispatch("all");
			return Promise.resolve(res);
		});
	}
};
