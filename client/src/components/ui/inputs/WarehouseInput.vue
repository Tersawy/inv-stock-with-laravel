<template>
	<b-form-group label="Warehouse" label-for="warehouse_id">
		<b-select id="warehouse_id" v-model.number="object.warehouse_id" :options="warehousesOpt" />
	</b-form-group>
</template>

<script>
	export default {
		props: ["object"],
		computed: {
			warehousesOpt() {
				return this.$store.state.Warehouse.options;
			}
		}
	};
</script>
