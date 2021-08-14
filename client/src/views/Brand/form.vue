<template>
	<div class="brand-form">
		<b-container fluid>
			<b-form @submit.prevent="handleSave">
				<DefaultInput type="text" placeholder="name" v-model="brand.name" field="name" />
				<DefaultInput type="text" placeholder="description" v-model="brand.description" field="description" />
				<input type="file" placeholder="image" @change="onFileChange" />
				<b-btn :variant="`${isUpdate ? 'success' : 'primary'}`" type="submit">save</b-btn>
			</b-form>
			<div style="max-width: 50px">
				<img :src="image" alt="" class="img-fluid" />
			</div>
		</b-container>
	</div>
</template>

<script>
	import { mapActions } from "vuex";
	import DefaultInput from "@/components/ui/DefaultInput.vue";
	import { formMixin } from "@/mixins";
	export default {
		components: { DefaultInput },

		props: ["oldItem"],

		mixins: [formMixin],

		data: () => ({
			brand: { name: null, description: null, image: null },
			image: null
		}),

		async mounted() {
			if (this.isUpdate) {
				this.brand = { ...this.oldItem, image: "" };
				this.image = this.APP_BRANDS_URL + this.oldItem.image;
			}
		},

		computed: {
			brandData() {
				let formData = new FormData();
				for (let k in this.brand) {
					formData.set(k, this.brand[k]);
				}

				return formData;
			}
		},

		methods: {
			...mapActions({
				create: "Brand/create",
				update: "Brand/update"
			}),

			handleSave() {
				if (this.isUpdate) return this.handleUpdate();

				return this.handleCreate();
			},

			async handleCreate() {
				await this.create(this.brandData);
				this.finished();
			},

			async handleUpdate() {
				await this.update(this.brandData);
				this.finished();
			},

			onFileChange(e) {
				var files = e.target.files || e.dataTransfer.files;
				if (!files.length) return;
				this.createImage(files[0]);
			},

			createImage(file) {
				var reader = new FileReader();
				var vm = this;

				this.brand.image = file;
				reader.onload = (e) => {
					vm.image = e.target.result;
				};
				reader.readAsDataURL(file);
			}
		}
	};
</script>
