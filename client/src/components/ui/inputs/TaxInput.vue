<template>
	<b-form-group label="Order Tax" label-for="tax">
		<b-input-group append="%">
			<b-form-input type="number" min="0" id="tax" placeholder="tax" v-model.number="object.tax" />
		</b-input-group>
	</b-form-group>
</template>

<script>
	export default {
		props: ["object"]
	};
</script>
