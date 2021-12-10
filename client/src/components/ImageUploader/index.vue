<template>
	<div
		:class="`image-uploader slider-${sliderPosition}`"
		@drag="preventEvent"
		@dragstart="preventEvent"
		@dragend="preventEvent"
		@dragover="preventEvent"
		@dragenter="preventEvent"
		@dragleave="preventEvent"
		@drop="preventEvent"
	>
		<div class="upload-area" :class="{ active: isDragover }">
			<label class="dragable-area" :for="inputId" @drop="onDrop" @dragover.prevent="onDragover" @dragleave="onDragleave"> </label>

			<div class="placeholder" @dragover.prevent="onDragover" @dragleave="onDragleave">
				<template v-if="isDragover">
					<IconImage class="placeholder-image-drag" />
					<div class="placeholder-text drop">Drop your images here.</div>
				</template>

				<template v-else>
					<template v-if="!images.length">
						<IconDrag class="placeholder-icon-drag" />
						<div class="placeholder-text">Drag and drop your images here or click to select images to upload.</div>
					</template>

					<template v-else>
						<image-review :no-review="noReview" :imageReview="imageReview">
							<template #images-list>
								<image-list :inputId="inputId" direction="row">
									<template #image-item>
										<image-item v-for="(image, i) in images" :key="i" :image="image" @delete-image="deleteImage" @click="onClickImage(image)" />
									</template>
								</image-list>
							</template>
						</image-review>
					</template>
				</template>
			</div>
		</div>

		<image-list v-if="images.length" :inputId="inputId" class="btn-darker" :direction="sliderDirection">
			<template #image-item v-if="!noSlider">
				<image-item v-for="(image, i) in images" :key="i" :image="image" @delete-image="deleteImage" @click="onClickImage(image)" />
			</template>
		</image-list>

		<input accept="image/*" type="file" :id="inputId" ref="inputUploader" style="display: none" :multiple="multiple" @change="uploadImages" />
	</div>
</template>

<script>
	import ImageItem from "./ImageItem.vue";
	import IconDrag from "./icons/IconDrag.vue";
	import IconImage from "./icons/IconImage.vue";
	import ImageList from "./ImageList.vue";
	import ImageReview from "./ImageReview.vue";
	export default {
		name: "ImageUploader",
		components: { IconDrag, IconImage, ImageList, ImageReview, ImageItem },
		props: {
			dataImages: {
				type: Array,
				default: () => []
			},
			max: {
				type: Number,
				default: () => 5
			},
			multiple: {
				type: Boolean,
				default: () => false
			},
			inputId: {
				type: String,
				default: () => "image-uploader-input"
			},
			disabled: {
				type: Boolean,
				default: () => false
			},
			noReview: {
				type: Boolean,
				default: () => false
			},
			noSlider: {
				type: Boolean,
				default: () => false
			},
			sliderPosition: {
				type: String,
				default: "right",
				validator: (value) => ["top", "bottom", "right", "left"].includes(value)
			},
			sliderDirection: {
				type: String,
				default: "column",
				validator: (value) => ["row", "column"].includes(value)
			},
			paste: {
				type: Boolean,
				default: () => true
			},
			accept: {
				type: Array,
				default: () => ["jpg", "jpeg", "png", "gif"],
				validator: (value) => value.every((ext) => ["jpg", "jpeg", "png", "gif"].includes(ext))
			},
			maxSize: {
				type: Number,
				default: () => 5
			},
			acceptErrorMsg: {
				type: String,
				default: () => "File type not accepted."
			},
			maxSizeErrorMsg: {
				type: String,
				default: () => "File size is too large."
			},
			maxErrorMsg: {
				type: String,
				default: () => "Maximum number of images reached."
			}
		},

		data: () => ({
			images: [],
			isDragover: false
		}),

		mounted() {
			if (this.dataImages.length) {
				for (let i = 0; i < this.dataImages.length; i++) {
					let image = this.dataImages[i];

					let isDefault = image.default || false;

					let url = image.url || URL.createObjectURL(image);

					if (this.images.length && !this.multiple) break;

					this.images.push({ url, default: isDefault, index: i });

					if (i === this.max - 1) {
						break;
					}
				}

				let defaultImage = this.images.find((image) => image.default);

				if (!defaultImage) {
					this.images[0].default = true;
				}
			}
		},

		computed: {
			imageReview() {
				let defaultImage = this.images.find((image) => image.default);

				return defaultImage ? defaultImage.url || URL.createObjectURL(defaultImage.file) : "";
			}
		},

		methods: {
			onClickImage(image) {
				this.images.forEach((img) => (img.default = false));

				image.default = true;
			},

			preventEvent(e) {
				e.preventDefault();
				e.stopPropagation();
			},

			onDragover() {
				this.isDragover = true;
			},

			onDragleave() {
				this.isDragover = false;
			},

			onDrop(e) {
				this.isDragover = false;
				e.stopPropagation();
				e.preventDefault();

				this.$refs.inputUploader.files = e.dataTransfer.files;

				this.uploadImages();
			},

			uploadImages() {
				let files = this.$refs.inputUploader.files;

				if (this.disabled || !files || !files.length) return;

				if (!this.multiple) {
					let firstImage = { file: files[0], default: true, index: 0, url: URL.createObjectURL(files[0]) };

					if (!this.validate(firstImage)) return;

					let stop = false;

					this.$emit("upload-image", firstImage, () => (stop = true));

					if (stop) return;

					this.images = [firstImage];

					return;
				}

				if (!this.validateMax(files)) return;

				for (let i = 0; i < files.length; i++) {
					let file = files[i];

					let image = { file, default: false, index: this.images.length, url: URL.createObjectURL(file) };

					if (!this.validate(image)) continue;

					let stop = false;

					this.$emit("upload-image", image, () => (stop = true));

					if (stop) continue;

					this.images.push(image);

					if (this.images.length >= this.max || !this.multiple) break;
				}
			},

			deleteImage(image) {
				let stop = false;

				this.$emit("delete-image", image, () => (stop = true));

				if (stop) return;

				let realIndex = 0;

				this.images = this.images.filter((img, i) => {
					if (img.index !== image.index) return true;

					realIndex = i;

					return false;
				});

				if (image.default && this.images.length) {
					(this.images[realIndex] || this.images[realIndex - 1] || this.images[0]).default = true;
				}
			},

			onPaste(e) {
				let stop = false;

				this.$emit("paste", e, () => (stop = true));

				if (stop) return;

				this.$refs.inputUploader.files = e.clipboardData.files;

				this.uploadImages();
			},

			validateSize(image) {
				if (image.file.size > this.maxSize * 1024 * 1024) {
					this.$emit("error", { image, message: this.maxSizeErrorMsg });
					return false;
				}

				return true;
			},

			validateType(image) {
				if (!this.accept.includes(image.file.type.split("/")[1])) {
					this.$emit("error", { image, message: this.acceptErrorMsg });
					return false;
				}

				return true;
			},

			validateMax(files) {
				if (this.images.length + files.length > this.max) {
					let image = { file: files[0], default: false, index: this.images.length, url: URL.createObjectURL(files[0]) };

					this.$emit("error", { image, message: this.maxErrorMsg });

					return false;
				}

				return true;
			},

			validate(image) {
				if (!this.validateSize(image)) return false;
				if (!this.validateType(image)) return false;

				return true;
			}
		},

		created() {
			if (this.paste) {
				window.addEventListener("paste", this.onPaste);
			}
		},

		beforeDestroy() {
			if (this.paste) {
				window.removeEventListener("paste", this.onPaste);
			}
		}
	};
</script>

<style lang="scss" scoped>
	.image-uploader {
		width: 100%;
		height: 300px;
		display: flex;
		&.slider-bottom,
		&.slider-top {
			justify-content: flex-start;
			align-items: center;
			height: 500px;
		}
		&.slider-bottom {
			flex-direction: column;
			.image-list {
				margin-top: 10px;
			}
		}
		&.slider-top {
			flex-direction: column-reverse;
			.image-list {
				margin-bottom: 10px;
			}
		}
		&.slider-right {
			flex-direction: row;
			.image-list {
				margin-left: 10px;
			}
		}
		&.slider-left {
			flex-direction: row-reverse;
			.image-list {
				margin-right: 10px;
			}
		}
		.btn-add-new {
			padding: 10px 25px;
			&:hover {
				border-color: rgb(255, 0, 0);
			}
			&:hover::before,
			&:hover::after {
				background-color: rgb(87, 0, 0);
			}
		}
		.upload-area {
			border: 1px dashed #ccc;
			border-radius: 8px;
			height: 100%;
			width: 100%;
			display: flex;
			justify-content: center;
			align-items: center;
			position: relative;
			overflow: hidden;
			.dragable-area {
				position: absolute;
				cursor: pointer;
				width: 100%;
				height: 100%;
				left: 0;
				background-color: transparent;
			}
			.placeholder {
				display: flex;
				justify-content: center;
				align-items: center;
				flex-direction: column;
				width: 100%;
				height: 100%;
				text-align: center;
				.placeholder-icon-drag,
				.placeholder-image-drag {
					fill: #ccc;
					height: 150px;
				}
				.placeholder-text {
					color: rgb(136, 136, 136);
					font-size: 14px;
					margin-top: 50px;
					&.drop {
						color: #007bff;
					}
				}
			}
			&.active {
				border-width: 0;
				.placeholder-image-drag {
					fill: #007bff;
				}
				// pulled from https://stackoverflow.com/a/57382510
				background: linear-gradient(90deg, #007bff 50%, transparent 50%), linear-gradient(90deg, #007bff 50%, transparent 50%),
					linear-gradient(0deg, #007bff 50%, transparent 50%), linear-gradient(0deg, #007bff 50%, transparent 50%);
				background-repeat: repeat-x, repeat-x, repeat-y, repeat-y;
				background-size: 15px 4px, 15px 4px, 4px 15px, 4px 15px;
				animation: border-dance 6s infinite linear;
				@keyframes border-dance {
					0% {
						background-position: 0 0, 100% 100%, 0 100%, 100% 0;
					}
					100% {
						background-position: 100% 0, 0 100%, 0 0, 100% 100%;
					}
				}
			}
		}
	}
</style>
