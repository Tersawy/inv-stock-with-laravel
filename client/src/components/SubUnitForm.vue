<template>
	<b-modal
		:id="modalId"
		@hidden="modalClosed(subUnit)"
		@show="modalOpened"
		hide-footer
		:header-bg-variant="modalVariant"
		header-text-variant="white"
	>
		<template #modal-header="{ close }">
			<h5 class="mb-0" v-if="isUpdate">
				<span>Update sub unit {{ oldItem.name }} for </span>
				<b-badge variant="primary"> {{ mainUnit.name }} ({{ mainUnit.short_name }}) </b-badge>
			</h5>
			<h5 class="mb-0" v-else>
				<span>Create a new sub unit for </span>
				<b-badge variant="primary"> {{ mainUnit.name }} ({{ mainUnit.short_name }}) </b-badge>
			</h5>
			<span size="sm" class="close-btn" @click="close()"><i class="fa fa-times fa-fw fa-lg"></i></span>
		</template>
		<div class="subUnit-form">
			<b-container fluid>
				<b-form @submit.prevent="handleSave">
					<DefaultInput type="text" placeholder="name" v-model="subUnit.name" field="name" />
					<DefaultInput type="text" placeholder="short name" v-model="subUnit.short_name" field="short_name" />
					<DefaultInput type="number" placeholder="value" v-model="subUnit.value" field="value" />
					<b-btn :variant="modalVariant" type="submit">save</b-btn>
				</b-form>
			</b-container>
		</div>
	</b-modal>
</template>

<script>
	import { mapActions } from "vuex";
	import InputError from "@/components/ui/InputError.vue";
	import DefaultInput from "@/components/ui/DefaultInput.vue";
	import { formMixin } from "@/mixins";
	export default {
		components: { DefaultInput, InputError },

		props: ["oldItem", "mainUnit"],

		mixins: [formMixin],

		data: () => ({
			subUnit: { name: null, short_name: null, value: null, main_unit: null },
			createVariant: "warning",
			modalId: "sub-unit-modal"
		}),

		methods: {
			...mapActions({
				create: "MainUnit/createSubUnit",
				update: "MainUnit/updateSubUnit"
			}),

			modalOpened() {
				if (this.isUpdate) {
					this.subUnit = { ...this.subUnit, ...this.oldItem };
				}
				this.subUnit.main_unit = this.mainUnit.id;
			},

			handleSave() {
				if (this.isUpdate) return this.handleUpdate();

				return this.handleCreate();
			},

			async handleCreate() {
				await this.create(this.subUnit);
				this.finished();
			},

			async handleUpdate() {
				await this.update(this.subUnit);
				this.finished();
			}
		}
	};
</script>
