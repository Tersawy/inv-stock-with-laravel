<template>
	<b-form-group label="Supplier" label-for="supplier_id">
		<b-select id="supplier_id" v-model.number="object.supplier_id" :options="suppliersOpt" />
	</b-form-group>
</template>

<script>
	export default {
		props: ["object"],
		computed: {
			suppliersOpt() {
				return this.$store.state.Supplier.options;
			}
		}
	};
</script>
