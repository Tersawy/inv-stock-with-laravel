import { mapMutations, mapState } from "vuex";

const errorMixin = {
	computed: {
		...mapState({
			errors: (state) => state.errors
		})
	},

	methods: {
		...mapMutations(["removeErrors", "removeError"])
	}
};

export default errorMixin;
