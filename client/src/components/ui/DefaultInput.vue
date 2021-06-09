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
		<InputError :field="field" />
	</b-form-group>
</template>

<script>
	import InputError from "@/components/ui/InputError.vue";
	export default {
		components: { InputError },
		props: {
			placeholder: String,
			field: String,
			type: String,
			label: String,
			value: "",
			disabled: Boolean
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
