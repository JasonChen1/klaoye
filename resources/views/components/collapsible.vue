<template>
	<div :class="isMobile?'':'col'">
		<div v-if="!isMobile">
			<h4 class="pb-3 footer-title" v-if="title">{{title}}</h4>
			<slot></slot>
		</div>
		<div class="mt-2" v-else>
			<button type="button" class="collapsible" @click="collapse=!collapse" v-if="title"><b-icon :icon="collapse?'chevron-right':'chevron-down'" size="is-small"></b-icon>{{title}}</button>
			<transition name="slide" >
				<div class="content" v-if="!collapse">
					<slot></slot>
				</div>
			</transition>
		</div>
	</div>
</template>

<script>
	export default {
		name: 'Collapsible',
		props:{
			title:{
                type:String,
                required:true
            },
            isMobile:{
                type:Boolean,
                required:true
            }
		},
		data() {
            return {
                collapse:true,
            }
        },
	}
</script>
