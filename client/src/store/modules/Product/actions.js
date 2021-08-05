import api from "@/plugins/api";

export default {
	details: ({ commit, state }, item) => {
		return api("post", `${state.prefix}/details`, item, (err, res) => {
			if (err) return;
			commit("details", res);
		});
	}
};
