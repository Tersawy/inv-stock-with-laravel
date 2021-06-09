import Vue from "vue";
import userMixin from "@/mixins/userMixin";
import errorMixin from "@/mixins/errorMixin";
import formMixin from "@/mixins/formMixin";

let runGlobalMixins = () => {
	Vue.mixin(userMixin);
	Vue.mixin(errorMixin);
};

export { runGlobalMixins, formMixin };
