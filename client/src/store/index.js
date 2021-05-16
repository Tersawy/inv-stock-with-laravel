import Vue from "vue";
import Vuex from "vuex";

import modules from "./modules";
import global from "./global";

Vue.use(Vuex);

const store = new Vuex.Store({ ...global, modules: { ...modules } });

export default store;
export { store };
