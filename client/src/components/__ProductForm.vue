<template>
	<div class="product-form">
		<b-container fluid>
			<b-form @submit.prevent="handleSave">
				<b-row>
					<b-col cols="8">
						<b-row cols="2">
							<b-col>
								<b-form-group label="Product Name" label-for="name">
									<b-form-input id="name" placeholder="Enter Product Name" v-model="product.name" />
									<InputError field="name" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Barcode Symbology" label-for="barcode_type">
									<b-select id="barcode_type" v-model="product.barcode_type" :options="barcodeOpt" @change="generateCode" />
									<InputError field="barcode_type" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Code" label-for="code">
									<b-input-group>
										<b-form-input id="code" placeholder="code" v-model="product.code" disabled />
										<b-input-group-prepend is-text>
											<span class="mb-0 c-pointer" @click="generateCode">
												<b-icon icon="upc"></b-icon>
											</span>
										</b-input-group-prepend>
									</b-input-group>
									<InputError field="code" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Price" label-for="price">
									<b-form-input id="price" type="number" placeholder="Enter Product Price" v-model.number="product.price" />
									<InputError field="price" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Cost" label-for="cost">
									<b-form-input id="cost" type="number" placeholder="Enter Product Cost" v-model.number="product.cost" />
									<InputError field="cost" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Minimum Alert" label-for="minimum">
									<b-form-input
										id="minimum"
										type="number"
										placeholder="Enter Product Minimum Alert"
										v-model.number="product.minimum"
									/>
									<InputError field="minimum" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Order Tax" label-for="tax">
									<b-input-group append="%">
										<b-form-input id="tax" placeholder="tax" v-model.number="product.tax" />
									</b-input-group>
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Tax Type" label-for="tax_method">
									<b-select id="tax_method" v-model.number="product.tax_method" :options="taxMethodOpt" />
									<InputError field="tax_method" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Category" label-for="category_id">
									<b-select id="category_id" v-model.number="product.category_id" :options="categoriesOpt" />
									<InputError field="category_id" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Brand" label-for="brand">
									<b-select id="brand" v-model.number="product.brand_id" :options="brandsOpt" />
									<InputError field="brand" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Unit" label-for="main_unit_id">
									<b-select id="main_unit_id" v-model.number="product.main_unit_id" :options="unitsOpt" @change="setSubUnits" />
									<InputError field="main_unit_id" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Purchase Unit" label-for="purchase_unit">
									<b-select id="purchase_unit" v-model.number="product.purchase_unit_id" :options="purchaseUnitsOpt" />
									<InputError field="purchase_unit" />
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Sale Unit" label-for="sale_unit">
									<b-select id="sale_unit" v-model.number="product.sale_unit_id" :options="saleUnitsOpt" />
									<InputError field="sale_unit" />
								</b-form-group>
							</b-col>
						</b-row>
					</b-col>
					<b-col>
						<UploadImages @change="handleImages" />
					</b-col>
				</b-row>
				<b-btn :variant="`${isUpdate ? 'success' : 'primary'}`" type="submit">save</b-btn>
			</b-form>
		</b-container>
	</div>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	import InputError from "@/components/ui/InputError.vue";
	import DefaultInput from "@/components/ui/DefaultInput.vue";
	import { formMixin } from "@/mixins";
	import UploadImages from "vue-upload-drop-images";
	export default {
		components: { DefaultInput, InputError, UploadImages },

		mixins: [formMixin],

		data: () => ({
			product: {
				name: null,
				barcode_type: "CODE128",
				code: null,
				price: null,
				cost: null,
				minimum: 0,
				tax: 0,
				tax_method: 1,
				note: null,
				category_id: null,
				brand_id: 0,
				main_unit_id: null,
				purchase_unit_id: null,
				sale_unit_id: null,
				has_variants: false,
				has_images: false
			},
			purchaseUnitsOpt: [{ text: "Choose Purchase Unit", value: null, disabled: true }],
			saleUnitsOpt: [{ text: "Choose Sale Unit", value: null, disabled: true }],
			barcodeOpt: [
				{ text: "Choose Symbology", value: null, disabled: true },
				{ text: "Code 128", value: "CODE128", codeLength: 8, disabled: false },
				{ text: "Code 39", value: "CODE39", codeLength: 8, disabled: false },
				{ text: "EAN8", value: "EAN-8", codeLength: 7, disabled: false },
				{ text: "EAN13", value: "EAN-13", codeLength: 12, disabled: false },
				{ text: "UPC", value: "UPC", codeLength: 11, disabled: false }
			],
			taxMethodOpt: [
				{ text: "Choose Symbology", value: null, disabled: true },
				{ text: "Inclusive", value: 1, disabled: false },
				{ text: "Exclusive", value: 0, disabled: false }
			],
			images: [],
			variants: []
		}),

		async mounted() {
			this.generateCode();
			this.getCategoriesOpt();
			this.getBrandsOpt();
			this.getUnitsOpt();

			if (this.isUpdate) {
				await this.getProduct(this.$route.params.productId);
				this.product = { ...this.oldProduct };
				this.setSubUnits(this.product.main_unit_id);
			}
		},

		computed: {
			...mapState({
				categoriesOpt: (state) => state.Category.options,
				brandsOpt: (state) => state.Brand.options,
				unitsOpt: (state) => state.MainUnit.options,
				oldProduct: (state) => state.Product.one
			}),

			isUpdate() {
				return this.$route.params.productId;
			}
		},

		methods: {
			...mapActions({
				create: "Product/create",
				update: "Product/update",
				getCategoriesOpt: "Category/options",
				getBrandsOpt: "Brand/options",
				getUnitsOpt: "MainUnit/options",
				getProduct: "Product/one"
			}),

			handleImages(files) {
				this.images = files;
			},

			setSubUnits(v) {
				let mainUnit = this.unitsOpt.find((opt) => opt.value == v);

				this.purchaseUnitsOpt = [this.purchaseUnitsOpt[0], ...mainUnit.sub_units];
				this.saleUnitsOpt = [this.saleUnitsOpt[0], ...mainUnit.sub_units];
			},

			generateCode() {
				this.product.barcode_type = this.product.barcode_type || this.barcodeOpt[1].value;

				let opt = this.barcodeOpt.find((opt) => opt.value == this.product.barcode_type);

				let length = opt.codeLength;

				let code = this.randomInteger(length);

				this.product.code = code.toString();
			},

			randomInteger(length) {
				let min, max;
				min = +(1 + Array(length).join("0"));
				max = +`${min}0`;
				return Math.floor(Math.random() * (max - min)) + min;
			},

			handleSave() {
				if (this.isUpdate) return this.handleUpdate();

				return this.handleCreate();
			},

			async handleCreate() {
				this.product.has_images = false;
				this.product.has_variants = false;

				if (this.images.length) {
					this.product.has_images = true;
					this.product.images = this.images;
				}

				if (this.variants.length) {
					this.product.has_variants = true;
					this.product.variants = this.variants;
				}

				let formData = new FormData();
				let self = this;

				for (let k in this.product) {
					if (k !== "images") {
						formData.append(k, self.product[k]);
					}
				}

				this.images.forEach((img, i) => {
					formData.append(`images[${i}][image]`, img);
				});

				await this.create(formData);

				this.finished();
			},

			async handleUpdate() {
				await this.update(this.product);
				this.finished();
			}
		}
	};
</script>
