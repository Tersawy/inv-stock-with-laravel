<template>
	<div class="category-form">
		<b-container fluid>
			<b-form @submit.prevent="handleSave">
				<DefaultInput type="text" placeholder="name" v-model="category.name" field="name" />
				<DefaultInput type="text" placeholder="code" v-model="category.code" field="code" />
				<b-btn :variant="`${isUpdate ? 'success' : 'primary'}`" type="submit">save</b-btn>
			</b-form>
		</b-container>
	</div>
</template>

<script>
	import { mapActions } from "vuex";
	import DefaultInput from "@/components/ui/DefaultInput.vue";
	import { formMixin } from "@/mixins";
	export default {
		components: { DefaultInput },

		mixins: [formMixin],

		props: ["oldItem"],

		data: () => ({
			category: { name: null, code: null }
		}),

		async mounted() {
			this.category = this.isUpdate ? { ...this.oldItem } : this.category;
		},

		computed: {
			isUpdate() {
				return this.oldItem && Object.keys(this.oldItem).length > 1;
			}
		},

		methods: {
			...mapActions({
				create: "Category/create",
				update: "Category/update"
			}),

			handleSave() {
				if (this.isUpdate) return this.handleUpdate();

				return this.handleCreate();
			},

			async handleCreate() {
				await this.create(this.category);
				this.finished();
			},

			async handleUpdate() {
				await this.update(this.category);
				this.finished();
			},

			finished() {
				if (!this.errors.name && !this.errors.code) {
					this.$emit("finished");
				}
			}
		}
	};
</script>
