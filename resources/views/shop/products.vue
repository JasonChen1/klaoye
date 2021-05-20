<template>
    <div >
        {{ message }}
    	<router-link :to="{ name: 'Home' }">HOME</router-link>
    </div>
</template>
<script>
    export default {
        data() {
          return {
            message: 'products page'
        }
    }
}
</script>