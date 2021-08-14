<template>
	<b-form-group label="Tax Type" label-for="tax_method">
		<b-select id="tax_method" v-model.number="object.tax_method" :options="taxMethods" />
	</b-form-group>
</template>

<script>
	export default {
		props: ["object"],
		computed: {
			taxMethods() {
				return this.$store.state.taxMethods;
			}
		}
	};
</script>
