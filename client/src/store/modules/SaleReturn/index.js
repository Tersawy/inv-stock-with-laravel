import state from "./state";
import mutations from "./mutations";
import actions from "./actions";
import sharededState from "@/store/shareded/all/state";
import sharededMutations from "@/store/shareded/all/mutations";
import sharededActions from "@/store/shareded/all/actions";
import invoicesState from "@/store/shareded/invoices/state";
import invoicesMutations from "@/store/shareded/invoices/mutations";
import invoicesActions from "@/store/shareded/invoices/actions";

export default {
	state: { ...sharededState, ...invoicesState, ...state },
	mutations: { ...sharededMutations, ...invoicesMutations, ...mutations },
	actions: { ...sharededActions, ...invoicesActions, ...actions },
	namespaced: true
};
