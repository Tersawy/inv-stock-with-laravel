import api from "@/plugins/api";

const payments = ({ commit, state }) => {
	api("get", `${state.prefix}/${state.one.id}/payments`, (err, res) => {
		if (err) return commit("setErrors", err);
		commit("payments", res);
	});
};

const createPayment = ({ state, commit, dispatch }, item) => {
	return api("post", `${state.prefix}/${state.one.id}/payments/create`, item, (err, res) => {
		if (err) {
			commit("setErrors", err);
			return Promise.reject(err);
		}
		dispatch("payments");
		return Promise.resolve(res);
	});
};

const updatePayment = ({ state, commit, dispatch }, item) => {
	return api("put", `${state.prefix}/${state.one.id}/payments/${item.id}`, item, (err, res) => {
		if (err) {
			commit("setErrors", err);
			return Promise.reject(err);
		}
		dispatch("payments");
		return Promise.resolve(res);
	});
};

const removePayment = ({ commit, state }, item) => {
	return api("post", `${state.prefix}/${state.one.id}/payments/${item.id}`, item, (err, res) => {
		if (err) {
			commit("setErrors", err);
			return Promise.reject(err);
		}
		commit("removePayment", item.id);
		return Promise.resolve(res);
	});
};

export default { payments, createPayment, updatePayment, removePayment };
