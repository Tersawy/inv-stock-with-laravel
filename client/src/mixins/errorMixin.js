import { mapMutations, mapState } from "vuex";

export default {
	computed: {
		...mapState({
			errors: (state) => state.errors
		})
	},

	methods: {
		...mapMutations(["setError", "removeError", "removeErrors"])
	}
};
