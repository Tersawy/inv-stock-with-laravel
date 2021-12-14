import axios from "@/plugins/axios";

export default {
	async all({ commit, state }, queries = "") {
		try {
			let data = await axios.get(state.prefix + queries);
			commit("all", data);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async options({ commit, state }) {
		try {
			let data = await axios.get(`${state.prefix}/options`);
			commit("options", data);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async edit({ commit, state }, itemId) {
		try {
			let data = await axios.get(`${state.prefix}/${itemId}/edit`);
			commit("one", data);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async one({ commit, state }, itemId) {
		try {
			let data = await axios.get(`${state.prefix}/${itemId}`);
			commit("one", data);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async create({ state, commit, dispatch }, payload) {
		try {
			let data = await axios.post(`${state.prefix}/create`, payload);
			commit("one", data);
			dispatch("all");
			return data;
		} catch (error) {
			throw error;
		}
	},

	async update({ state, commit, dispatch }, payload) {
		try {
			let data = await axios.put(`${state.prefix}/${payload.id}`, payload);
			dispatch("all");
			return data;
		} catch (error) {
			throw error;
		}
	},

	async moveToTrash({ commit, state }, payload) {
		try {
			let data = await axios.post(`${state.prefix}/${payload.id}/trash`, payload);
			commit("remove", payload.id);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async trashed({ commit, state }) {
		try {
			let data = await axios.get(`${state.prefix}/trashed`);
			commit("all", data);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async restore({ commit, state }, payload) {
		try {
			let data = await axios.post(`${state.prefix}/${payload.id}/restore`);
			commit("remove", payload.id);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async remove({ commit, state }, payload) {
		try {
			let data = await axios.post(`${state.prefix}/${payload.id}`);
			commit("remove", payload.id);
			return data;
		} catch (error) {
			throw error;
		}
	}
};
