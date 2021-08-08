<template>
	<b-form-group label="Status" label-for="status">
		<b-select id="status" v-model.number="invoice.status" :options="statusOpts" />
	</b-form-group>
</template>

<script>
	export default {
		props: ["invoice", "namespace"],
		computed: {
			statusOpts() {
				return this.$store.state[this.namespace].statusOptions;
			}
		}
	};
</script>
