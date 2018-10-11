<script>
import Dropdown from './dropdown';
import BDropdownItem from 'bootstrap-vue/es/components/dropdown/dropdown-item';
import PgIcon from 'prontogioco/app/components/icon';

export default {
	name: 'PgExplorePageFilterButton',

	components: {
		Dropdown,
		BDropdownItem,
		PgIcon
	},

	props: {
		options: {
			type: Array,
			default: () => []
		},
		label: {
			type: String,
			required: true
		},
		text: {
			type: String,
			default: ''
		},
		emptyText: {
			type: String,
			default: function() {
				return `${this.$t('common.actions.select')}...`;
			}
		},
		allText: {
			type: String,
			default: function() {
				return this.$t('pages.explore.filters.all');
			}
		},
		value: {
			type: [String, Number, Array],
			default: null
		},
		multiple: {
			type: Boolean,
			default: false
		}
	},

	data() {
		return {
			mutableValue: this.value
		};
	},

	computed: {
		icon() {
			return this.multiple ? 'checkmark' : 'bullet';
		},

		automaticText() {
			const v = this.value;

			if (!v) return;

			if (this.multiple && v instanceof Array) {
				if (v.length === 0) {
					return this.emptyText;
				}

				if (v.length === 1) {
					const option = this.options.find(option => option.value === v[0]);
					return option ? option.label : '';
				}

				if (v.length === this.options.length && this.allText) {
					return this.allText;
				}

				return this.$tc('pages.explore.filters.selected', v.length, { count: v.length });
			} else {
				const selectedOption = this.options.find(option => option.value === v);

				return selectedOption ? selectedOption.label : `${this.$t('common.actions.select')}...`;
			}

		}
	},

	methods: {
		select(option) {
			let value = this.value;

			if (this.multiple) {
				// Convert non array to array
				if (!(value instanceof Array)) value = [value];

				// Add the new value if not present, or remove it if present
				const index = value.indexOf(option.value);
				if (index >= 0) {
					value.splice(index, 1);
				} else {
					value.push(option.value);
				}

				// Strip null values
				value = value.filter(v => v !== null);
			} else {
				value = option.value;
			}

			this.mutableValue = value;

			this.$emit('input', this.mutableValue);
		},

		isSelected(option) {
			if (this.multiple && this.mutableValue instanceof Array) {
				return this.mutableValue.indexOf(option.value) >= 0;
			} else {
				return this.mutableValue === option.value;
			}
		},

		onHide() {
			if (this.value !== this.mutableValue) this.$emit('change', this.mutableValue);
		}
	}
};
</script>

<template>
	<dropdown
		:auto-hide="!multiple"
		variant="link"
		toggle-class="filter-button"
		no-caret
		@hide="onHide">
		<template slot="button-content">
			<div>
				<span class="filter-button__label">{{ label }}</span>
				<pg-icon icon="chevron-down" class="filter-button__arrow" />
			</div>
			<div class="filter-button__text">{{ text || automaticText }}</div>
		</template>

		<b-dropdown-item
			v-for="option in options"
			:key="option.value"
			@click.prevent="select(option)">
			<div class="d-flex align-items-center">
				<pg-icon :icon="icon" :class="['filter-button-menu__icon', isSelected(option) ? '' : 'invisible']" />
				<div class="filter-button-menu__text">{{ option.label }}</div>
			</div>
		</b-dropdown-item>
	</dropdown>
</template>