import Vue from "vue";
import userMixin from "@/mixins/userMixin";
import errorMixin from "@/mixins/errorMixin";
import formMixin from "@/mixins/formMixin";
import invoiceMixin from "@/mixins/invoiceMixin";

let runGlobalMixins = () => {
	Vue.mixin(userMixin);
	Vue.mixin(errorMixin);
};

export { runGlobalMixins, formMixin, invoiceMixin };
