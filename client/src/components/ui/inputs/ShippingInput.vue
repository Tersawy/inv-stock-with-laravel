<template>
	<b-form-group label="Shipping cost" label-for="shipping">
		<b-input-group append="$">
			<b-form-input type="number" min="0" id="shipping" placeholder="shipping" v-model.number="object.shipping" />
		</b-input-group>
	</b-form-group>
</template>

<script>
	export default {
		props: ["object"]
	};
</script>
