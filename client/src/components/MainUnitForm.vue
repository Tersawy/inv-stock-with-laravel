<template>
	<b-modal
		:id="modalId"
		@hidden="modalClosed(mainUnit)"
		@show="modalOpened"
		hide-footer
		:header-bg-variant="modalVariant"
		header-text-variant="white"
	>
		<template #modal-header="{ close }">
			<h5 class="mb-0">{{ modalTitle }}</h5>
			<span size="sm" class="close-btn" @click="close()"><i class="fa fa-times fa-fw fa-lg"></i></span>
		</template>
		<div class="mainUnit-form">
			<b-form @submit.prevent="handleSave">
				<DefaultInput type="text" placeholder="name" v-model="mainUnit.name" field="name" />
				<DefaultInput type="text" placeholder="short name" v-model="mainUnit.short_name" field="short_name" />
				<b-btn :variant="modalVariant" type="submit">save</b-btn>
			</b-form>
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

		props: ["oldItem"],

		mixins: [formMixin],

		data: () => ({
			mainUnit: { name: null, short_name: null },
			modalId: "main-unit-modal"
		}),

		async mounted() {
			this.mainUnit = this.isUpdate ? { ...this.oldItem } : this.mainUnit;
		},

		computed: {
			modalTitle() {
				return this.isUpdate ? `Update ${this.oldItem.name} (${this.oldItem.short_name})` : "Create a new main unit";
			}
		},

		methods: {
			...mapActions({
				create: "MainUnit/create",
				update: "MainUnit/update"
			}),

			modalOpened() {
				this.mainUnit = this.isUpdate ? { ...this.oldItem } : this.mainUnit;
			},

			handleSave() {
				if (this.isUpdate) return this.handleUpdate();

				return this.handleCreate();
			},

			async handleCreate() {
				await this.create(this.mainUnit);
				this.finished();
			},

			async handleUpdate() {
				await this.update(this.mainUnit);
				this.finished();
			}
		}
	};
</script>
