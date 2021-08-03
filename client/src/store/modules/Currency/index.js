import state from "./state";
import mutations from "./mutations";
import actions from "./actions";
import sharededState from "../../shareded/state";
import sharededMutations from "../../shareded/mutations";
import sharededActions from "../../shareded/actions";

export default {
	state: { ...sharededState, ...state },
	mutations: { ...sharededMutations, ...mutations },
	actions: { ...sharededActions, ...actions },
	namespaced: true
};
