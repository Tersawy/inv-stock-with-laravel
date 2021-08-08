import api from "@/plugins/api";

export default {
	details: ({ commit, state }, item) => {
		return api("post", `${state.prefix}/details`, item, (err, res) => {
			if (err) return;
			commit("details", res);
		});
	},

	create: ({ state, dispatch }, item) => {
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
	}
};
