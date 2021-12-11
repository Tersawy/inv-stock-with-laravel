<template>
	<div class="stepper">
		<div class="stepper-header">
			<template v-for="(step, index) in steps">
				<div class="step-item" :key="index" :class="{ 'is-active': index === currentStep, 'is-completed': step.completed, 'no-title': hideTitle }">
					<div class="step-item-icon">
						<CheckIcon v-if="step.completed" />
						<template v-else>
							<component v-if="step.icon" :is="step.icon" />
							<span v-else class="step-item-icon-inner">{{ index + 1 }}</span>
						</template>
					</div>
					<p class="step-item-title" v-if="!hideTitle">{{ step.title }}</p>
				</div>
				<hr :key="`hr-${index}`" v-if="index !== steps.length - 1" />
			</template>
		</div>
		<div class="stepper-body">
			<div class="steps-wrapper" ref="stepsWrapper">
				<div class="step" v-for="(step, index) in steps" :key="index" :class="{ active: step.active, completed: step.completed }" :ref="`step`">
					<div class="step-body">
						<slot :name="`step(${step.name})`" :step="step" :next="nextStep" :prev="prevStep" :to="toStep" :complete="completeStep" :reset="resetStep">
							<div class="step-content">Welcome to Step {{ step.name }}</div>
						</slot>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<style lang="scss" scoped></style>

<script>
	import CheckIcon from "./icons/CheckIcon";

	export default {
		components: { CheckIcon },

		props: {
			steps: {
				type: Array,
				default: () => []
			},
			visibleTitleOn: {
				type: [Number, String],
				default: () => 768
			}
		},

		data() {
			return {
				hideTitle: false
			};
		},

		computed: {
			currentStep() {
				return this.steps.findIndex((step) => step.active);
			}
		},

		methods: {
			nextStep() {
				const currentStep = this.steps[this.currentStep];

				const nextIndex = this.currentStep === this.steps.length - 1 ? this.currentStep - 1 : this.currentStep + 1;

				const nextStep = this.steps[nextIndex];

				if (nextStep) {
					currentStep.active = false;

					nextStep.active = true;

					this.$refs.stepsWrapper.scrollLeft = this.$refs.step[this.currentStep].offsetLeft;

					if (nextStep.disabled) {
						this.nextStep();
					}
				}
			},

			prevStep() {
				const currentStep = this.steps[this.currentStep];

				const prevIndex = this.currentStep - 1 || 0;

				const prevStep = this.steps[prevIndex];

				if (prevStep) {
					currentStep.active = false;

					prevStep.active = true;

					this.$refs.stepsWrapper.scrollLeft = this.$refs.step[this.currentStep].offsetLeft;

					if (prevStep.disabled) {
						this.prevStep();
					}
				}
			},

			completeStep() {
				const currentStep = this.steps[this.currentStep];
				currentStep.completed = true;
			},

			resetStep() {
				const currentStep = this.steps[this.currentStep];
				currentStep.completed = false;
			},

			toStep(index) {
				const currentStep = this.steps[this.currentStep];
				currentStep.active = false;

				const nextStep = this.steps[index];
				nextStep.active = true;

				this.$refs.stepsWrapper.scrollLeft = this.$refs.step[index].offsetLeft;
			}
		},

		created() {
			let handler = (tab) => (this.hideTitle = tab.matches);

			// pulled from https://stackoverflow.com/a/56678176
			var tab = window.matchMedia(`(max-width: ${this.visibleTitleOn}px)`);

			handler(tab);

			tab.onchange = handler;
		}
	};
</script>

<style lang="scss" scoped>
	.stepper {
		.stepper-header {
			display: flex;
			width: 100%;
			align-items: stretch;
			flex-wrap: wrap;
			justify-content: space-between;
			background-color: #fff;
			color: #000;
			.step-item {
				align-items: center;
				display: flex;
				flex-direction: row;
				padding: 24px;
				position: relative;
				.step-item-icon {
					display: flex;
					align-items: center;
					justify-content: center;
					width: 24px;
					min-width: 24px;
					height: 24px;
					min-height: 24px;
					border-radius: 50%;
					font-size: 0.75rem;
					background-color: rgba(0, 0, 0, 0.38);
					color: #fff;
					margin-right: 8px;
				}
				.step-item-title {
					font-size: 1.1em;
					margin: 0;
				}
				&.is-active {
					.step-item-icon {
						background-color: #03a9f4;
					}
				}
				&.is-completed {
					.step-item-icon {
						background-color: #28a745;
						font-size: 1.3rem;
					}
				}
				&.no-title {
					justify-content: center;
					.step-item-icon {
						margin: 0;
					}
				}
			}
			hr {
				align-self: center;
				margin: 0 -16px;
				display: block;
				flex: 1 1 0px;
				max-width: 100%;
				height: 0;
				max-height: 0;
				border: solid;
				border-width: thin 0 0 0;
				overflow: visible;
				border-color: rgba(0, 0, 0, 0.12);
			}
		}
		.stepper-body {
			padding: 1rem;
			overflow: hidden;
			.steps-wrapper {
				display: flex;
				flex-direction: row;
				align-items: center;
				justify-content: flex-start;
				border-radius: 0.5rem;
				background-color: #fff;
				color: #000;
				overflow: hidden;
				position: relative;
				scroll-behavior: smooth;
				.step {
					min-width: 100%;
				}
			}
		}
	}
</style>
