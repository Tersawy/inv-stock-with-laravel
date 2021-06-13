<template>
	<div v-if="errors[field] && errors[field][0]" class="input-error text-danger">{{ errors[field][0] }}</div>
</template>

<script>
	export default {
		props: ["field"]
	};
</script>

<style lang="scss">
	.input-error {
		font-size: 10px;
		margin-top: 2px;
	}
</style>
