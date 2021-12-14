import axios from "@/plugins/axios";

export default {
	async details({ commit }, payload) {
		try {
			let data = await axios.post("/products/details", payload);
			commit("details", data);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async create({ dispatch }, payload) {
		try {
			let data = await axios.post("/products/create", payload);
			dispatch("all");
			return data;
		} catch (error) {
			throw error;
		}
	},

	async update({ dispatch }, item) {
		try {
			let data = await axios.put(`/products/${item.id}`, payload);
			dispatch("all");
			return data;
		} catch (error) {
			throw error;
		}
	}
};
