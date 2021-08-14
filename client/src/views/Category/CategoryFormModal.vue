<template>
	<b-modal
		id="categoryFormModal"
		centered
		@ok="handleOk"
		@hidden="resetModal"
		footer-class="justify-content-between"
		:no-close-on-esc="btnLoading"
		:no-close-on-backdrop="btnLoading"
	>
		<template #modal-header>
			<div class="d-flex justify-content-between w-100">
				<h5 class="m-0" v-if="!category">Create a new category</h5>
				<h5 class="m-0" v-else>
					Update category <span class="text-success">{{ category.name }}</span>
				</h5>
				<div class="h4 mb-0 c-pointer text-muted" @click="closeModal()">&times;</div>
			</div>
		</template>

		<template #modal-footer="{ ok, cancel }">
			<b-button size="sm" :disabled="btnLoading" v-if="!category" variant="secondary" @click.prevent="resetModal"> Reset </b-button>
			<div class="ml-auto">
				<b-button class="mx-3" size="sm" :variant="category ? 'success' : 'primary'" @click.prevent="ok" v-if="!btnLoading">
					{{ category ? "Update" : "Create" }}
				</b-button>
				<b-btn :variant="category ? 'success' : 'primary'" size="sm" class="mx-3" disabled v-else>
					<b-spinner class="text-sm" small type="grow"></b-spinner>
					Loading...
				</b-btn>
				<b-button size="sm" :disabled="btnLoading" variant="danger" @click="cancel"> Cancel </b-button>
			</div>
		</template>
		<b-form ref="form" @submit.prevent="handleOk">
			<b-form-group label="Category Name" label-for="name">
				<b-form-input class="mb-1" autocomplete="off" :disabled="btnLoading" id="name" v-model="categoryData.name"></b-form-input>
			</b-form-group>
		</b-form>
	</b-modal>
</template>

<script>
	import { mapActions } from "vuex";
	import { required, minLength, maxLength } from "vuelidate/lib/validators";
	export default {
		data() {
			return {
				categoryData: { name: null },
				btnLoading: false
			};
		},
		validations: {
			categoryData: {
				name: {
					required,
					minLength: minLength(4),
					maxLength: maxLength(55)
				}
			}
		},
		watch: {
			category(v) {
				if (v) {
					this.categoryData.name = this.category.name;
					this.categoryData.id = this.category.id;
				}
			}
		},
		computed: {
			category() {
				return this.$store.state.categoryUpdate;
			}
		},
		methods: {
			...mapActions({
				create: "Category/create",
				update: "Category/update"
			}),

			handleOk(bvModalEvt) {
				bvModalEvt.preventDefault();

				if (!this.category) return this.createCategory();

				this.updateCategory();
			},
			async handleCreate() {
				this.btnLoading = true;
				let c = await this.$store.dispatch("createCategory", this.categoryData);
				this.btnLoading = false;
				if (c) this.$bvModal.hide("createCategoryModal");
			},
			async handleUpdate() {
				this.btnLoading = true;
				let c = await this.$store.dispatch("updateCategory", this.categoryData);
				this.btnLoading = false;
				if (c) this.$bvModal.hide("createCategoryModal");
			},
			resetModal() {
				this.categoryData.name = null;
				this.categoryData.id = null;
				this.$store.state.categoryUpdate = null;
				this.removeErrors();
			},
			closeModal() {
				if (!this.btnLoading) {
					this.$bvModal.hide("createCategoryModal");
				}
			}
		}
	};
</script>
