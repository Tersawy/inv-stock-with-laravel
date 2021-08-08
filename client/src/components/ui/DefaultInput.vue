<template>
	<b-form-group :label="label" :label-for="id">
		<b-form-input
			:id="id"
			v-bind="$props"
			@input="
				$emit('input', $event);
				removeError(field);
			"
		/>
		<input-error :field="field" />
	</b-form-group>
</template>

<script>
	export default {
		props: {
			placeholder: String,
			field: String,
			type: String,
			label: String,
			value: "",
			disabled: Boolean,
			namespace: String
		},
		data: () => ({
			id: "input",
			counter: 1
		}),
		created() {
			this.id = "input" + this.counter++;
		}
	};
</script>
