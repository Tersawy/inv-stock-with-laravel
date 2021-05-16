<template>
	<div class="alert-msg">
		<div class="`alert alert-danger`"></div>
	</div>
</template>
<script>
	export default {
		// computed: {
		// 	...mapState({
		// 		successMsg: (state) => state.successMsg,
		// 		successDismissSecs: (state) => state.successDismissSecs,
		// 		errorMsg: (state) => state.errorMsg,
		// 		errorDismissSecs: (state) => state.errorDismissSecs
		// 	})
		// },
		// methods: {
		// 	...mapMutations(["removeSuccess", "removeError"])
		// }
	};
</script>
<style lang="sass">
	.alert-msg {
		position: absolute !important;
		top: 72px;
		left: -100%;
		transition: 0.2s ease-in-out;
		&.show {
			left: 16px;
		}
	}
</style>
