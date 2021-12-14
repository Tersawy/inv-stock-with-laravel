import axios from "@/plugins/axios";

export default {
	async login({ commit }, payload) {
		try {
			let data = await axios.post("login", payload);
			commit("login", data);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async logout({ commit }) {
		try {
			let data = await axios.post("logout");
			commit("logout", data);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async me({ commit }) {
		try {
			let data = await axios.get("me");
			commit("me", data);
			return data;
		} catch (error) {
			throw error;
		}
	},

	async resetPassword({ commit }, data) {
		let response = "This where is api action go";
		console.log(data);
		commit("resetPassword", response);
	},

	async verifiyToken({ commit }, data) {
		let response = "This where is api action go";
		console.log(data);
		commit("verifiyToken", response);
	},

	async newPassword({ commit }, data) {
		let response = "This where is api action go";
		console.log(data);
		commit("newPassword", response);
	},

	async updateProfile({ commit }, data) {
		let response = "This where is api action go";
		console.log(data);
		commit("updateProfile", response);
	}
};
