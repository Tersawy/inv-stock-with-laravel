import state from "./state";
import mutations from "./mutations";
import actions from "./actions";
import sharededState from "@/store/shareded/all/state";
import sharededMutations from "@/store/shareded/all/mutations";
import sharededActions from "@/store/shareded/all/actions";

export default {
	state: { ...sharededState, ...state },
	mutations: { ...sharededMutations, ...mutations },
	actions: { ...sharededActions, ...actions },
	namespaced: true
};
