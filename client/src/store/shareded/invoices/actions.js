import axios from "@/plugins/axios";

export default {
	async payments({ commit, state }) {
		try {
			let data = await axios.get(`${state.prefix}/${state.one.id}/payments`);
			commit("payments", data);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async createPayment({ state, commit, dispatch }, payload) {
		try {
			let data = await axios.post(`${state.prefix}/${state.one.id}/payments/create`, payload);
			commit("payments", data);
			dispatch("all");
			dispatch("payments");
			return data;
		} catch (error) {
			throw error;
		}
	},

	async updatePayment({ state, commit, dispatch }, payload) {
		try {
			let data = await axios.put(`${state.prefix}/${state.one.id}/payments/${payload.id}`, payload);
			commit("payments", data);
			dispatch("all");
			dispatch("payments");
			return data;
		} catch (error) {
			throw error;
		}
	},

	async removePayment({ state, commit, dispatch }, item) {
		try {
			let data = await axios.post(`${state.prefix}/${state.one.id}/payments/${item.id}`);
			commit("payments", data);
			dispatch("all");
			dispatch("payments");
			return data;
		} catch (error) {
			throw error;
		}
	}
};
